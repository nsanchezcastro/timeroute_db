<?php
class GestionVisitas {
    private $conn;
    private $table_asignaciones = "asignaciones";
    private $table_pacientes = "pacientes";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener la lista de pacientes del día para un trabajador específico
    public function obtenerHojaDeRuta($id_usuario) {
        $query = "SELECT 
                    a.id_asignacion, 
                    a.orden_visita, 
                    a.estado, 
                    p.nombre AS nombre_paciente, 
                    p.direccion, 
                    p.latitud, 
                    p.longitud
                  FROM " . $this->table_asignaciones . " a
                  JOIN " . $this->table_pacientes . " p ON a.id_paciente = p.id_paciente
                  WHERE a.id_usuario = :id 
                  AND a.fecha = CURDATE()
                  ORDER BY a.orden_visita ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id_usuario]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function iniciarVisita($id_asignacion, $lat_usuario, $long_usuario) {
    // 1. Obtener la ubicación real del paciente para esta asignación
    $query = "SELECT p.latitud, p.longitud, a.estado 
              FROM asignaciones a 
              JOIN pacientes p ON a.id_paciente = p.id_paciente 
              WHERE a.id_asignacion = :id_asig";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute([':id_asig' => $id_asignacion]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) return ["status" => "error", "message" => "Asignación no encontrada"];
    if ($datos['estado'] !== 'pendiente') return ["status" => "error", "message" => "Esta visita ya está en curso o finalizada"];

    // 2. Calcular distancia (Haversine) entre usuario y paciente
    $distancia = $this->calcularDistancia($lat_usuario, $long_usuario, $datos['latitud'], $datos['longitud']);

    // Permitimos un margen de 200 metros (por precisión del GPS móvil)
    if ($distancia > 200) {
        return [
            "status" => "error", 
            "message" => "Estás demasiado lejos del paciente (" . round($distancia) . "m). Acércate a la ubicación."
        ];
    }

    // 3. Si está cerca, iniciamos el registro
    $this->conn->beginTransaction();
    try {
        // Actualizamos estado de la asignación
        $sql1 = "UPDATE asignaciones SET estado = 'en_curso' WHERE id_asignacion = :id_asig";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->execute([':id_asig' => $id_asignacion]);

        // Creamos el registro de tiempo
        $sql2 = "INSERT INTO registros_visita (id_asignacion, hora_inicio) VALUES (:id_asig, NOW())";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([':id_asig' => $id_asignacion]);

        $this->conn->commit();
        return ["status" => "success", "message" => "Visita iniciada correctamente"];
    } catch (Exception $e) {
        $this->conn->rollBack();
        return ["status" => "error", "message" => "Error al guardar: " . $e->getMessage()];
    }
}

// Función auxiliar para calcular distancia en metros
private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
    $rad = M_PI / 180;
    $dLat = ($lat2 - $lat1) * $rad;
    $dLon = ($lon2 - $lon1) * $rad;
    $a = sin($dLat/2) * sin($dLat/2) + cos($lat1 * $rad) * cos($lat2 * $rad) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return 6371000 * $c; // Resultado en metros
}

public function crearPaciente($nombre, $direccion, $lat, $lng) {
    $query = "INSERT INTO " . $this->table_pacientes . " (nombre, direccion, latitud, longitud) 
              VALUES (:nombre, :direccion, :lat, :lng)";
    
    $stmt = $this->conn->prepare($query);
    
    return $stmt->execute([
        ':nombre'    => $nombre,
        ':direccion' => $direccion,
        ':lat'       => $lat,
        ':lng'       => $lng
    ]);
}

public function finalizarVisita($id_asignacion) {
    // 1. Buscamos el registro de inicio para esta asignación
    $query = "SELECT id_registro, hora_inicio FROM registros_visita 
              WHERE id_asignacion = :id_asig AND hora_fin IS NULL LIMIT 1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute([':id_asig' => $id_asignacion]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registro) return ["status" => "error", "message" => "No hay una visita activa para finalizar"];

    // 2. Calculamos la diferencia de tiempo
    $inicio = new DateTime($registro['hora_inicio']);
    $fin = new DateTime(); // Hora actual
    $intervalo = $inicio->diff($fin);
    
    // Convertimos a minutos totales
    $minutos = ($intervalo->h * 60) + $intervalo->i + ($intervalo->days * 1440);

    // 3. Actualizamos las tablas (Transacción para asegurar que ambas cambian o ninguna)
    $this->conn->beginTransaction();
    try {
        // Marcamos la visita como completada en la hoja de ruta
        $sql1 = "UPDATE asignaciones SET estado = 'completado' WHERE id_asignacion = :id_asig";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->execute([':id_asig' => $id_asignacion]);

        // Guardamos la hora de fin y los minutos en el registro
        $sql2 = "UPDATE registros_visita 
                 SET hora_fin = NOW(), minutos_visita = :min 
                 WHERE id_registro = :id_reg";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([
            ':min' => $minutos,
            ':id_reg' => $registro['id_registro']
        ]);

        $this->conn->commit();
        return [
            "status" => "success", 
            "minutos_dedicados" => $minutos,
            "message" => "Visita finalizada. Tiempo registrado: $minutos min."
        ];
    } catch (Exception $e) {
        $this->conn->rollBack();
        return ["status" => "error", "message" => "Error al finalizar: " . $e->getMessage()];
    }
}

public function asignarVisita($id_usuario, $id_paciente, $fecha, $orden) {
    // 1. Verificar si ya existe esa asignación exacta
    $check = "SELECT id_asignacion FROM asignaciones 
              WHERE id_usuario = :u AND id_paciente = :p AND fecha = :f";
    $stmt_check = $this->conn->prepare($check);
    $stmt_check->execute([':u' => $id_usuario, ':p' => $id_paciente, ':f' => $fecha]);

    if ($stmt_check->rowCount() > 0) {
        return ["status" => "error", "message" => "Este trabajador ya tiene asignado este paciente para ese día."];
    }

    // 2. Insertar la nueva ruta
    $query = "INSERT INTO asignaciones (id_usuario, id_paciente, fecha, orden_visita, estado) 
              VALUES (:u, :p, :f, :o, 'pendiente')";
    
    $stmt = $this->conn->prepare($query);
    $resultado = $stmt->execute([
        ':u' => $id_usuario,
        ':p' => $id_paciente,
        ':f' => $fecha,
        ':o' => $orden
    ]);

    if ($resultado) {
        return ["status" => "success", "message" => "Ruta asignada correctamente."];
    }
    return ["status" => "error", "message" => "No se pudo realizar la asignación."];
}
}
