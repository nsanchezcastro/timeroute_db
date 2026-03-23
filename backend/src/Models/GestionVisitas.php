<?php
class GestionVisitas {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener la hoja de ruta completa (Jornada + Sus Visitas)
     */
    public function obtenerHojaDeRuta($id_usuario) {
        // 1. Buscamos la jornada activa de hoy para el usuario
        $queryJornada = "SELECT 
                            j.id, 
                            j.fecha, 
                            j.estado, 
                            r.nombre AS nombre_ruta, 
                            j.pausa_activa 
                        FROM jornadas j
                        JOIN rutas r ON j.id_ruta = r.id
                        WHERE j.id_usuario = :id 
                        AND j.fecha = CURDATE() 
                        LIMIT 1";
        
        $stmtJ = $this->conn->prepare($queryJornada);
        $stmtJ->execute([':id' => $id_usuario]);
        $jornada = $stmtJ->fetch(PDO::FETCH_ASSOC);

        if (!$jornada) return null;

        // 2. Buscamos todas las visitas (paradas) asociadas a esa jornada
        $queryVisitas = "SELECT 
                            v.id_cliente, 
                            c.nombre AS nombre_cliente, 
                            c.direccion, 
                            c.latitud AS lat, 
                            c.longitud AS lng,
                            v.orden,
                            v.llegada,
                            v.salida,
                            v.duracion_minutos
                         FROM visitas v
                         JOIN clientes c ON v.id_cliente = c.id_cliente
                         WHERE v.id_jornada = :id_jornada
                         ORDER BY v.orden ASC";

        $stmtV = $this->conn->prepare($queryVisitas);
        $stmtV->execute([':id_jornada' => $jornada['id_jornada']]);
        
        // Añadimos las paradas al array de la jornada
        $jornada['paradas'] = $stmtV->fetchAll(PDO::FETCH_ASSOC);

        return $jornada;
    }

    /**
     * Iniciar jornada (Cambiar estado de CREADA a EN_CURSO)
     */
    public function iniciarJornada($id_jornada) {
        $query = "UPDATE jornadas SET estado = 'EN_CURSO', inicio_real = NOW() WHERE id_jornada = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id_jornada]);
    }

    /**
     * Finalizar jornada
     */
    public function finalizarJornada($id_jornada) {
        $query = "UPDATE jornadas SET estado = 'FINALIZADA', fin_real = NOW() WHERE id_jornada = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id_jornada]);
    }

    /**
     * Registrar llegada (Validación de distancia incluida)
     */
    public function registrarLlegada($id_jornada, $id_cliente, $lat_movil, $lng_movil) {
        // 1. Obtener ubicación del cliente
        $query = "SELECT latitud, longitud FROM clientes WHERE id_cliente = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id_cliente]);
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) return ["status" => "error", "message" => "Cliente no encontrado"];

        // 2. Calcular distancia
        $distancia = $this->calcularDistancia($lat_movil, $lng_movil, $cliente['latitud'], $cliente['longitud']);

        if ($distancia > 200) {
            return ["status" => "error", "message" => "Demasiado lejos (" . round($distancia) . "m)"];
        }

        // 3. Update en la tabla visitas
        $upd = "UPDATE visitas SET llegada = NOW() WHERE id_jornada = :jId AND id_cliente = :cId";
        $stmtUpd = $this->conn->prepare($upd);
        $stmtUpd->execute([':jId' => $id_jornada, ':cId' => $id_cliente]);

        return ["status" => "success", "distancia" => round($distancia, 2)];
    }

    /**
     * Registrar salida y calcular duración
     */
    public function registrarSalida($id_jornada, $id_cliente) {
        $query = "UPDATE visitas 
                  SET salida = NOW(), 
                      duracion_minutos = TIMESTAMPDIFF(MINUTE, llegada, NOW()) 
                  WHERE id_jornada = :jId AND id_cliente = :cId AND llegada IS NOT NULL";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':jId' => $id_jornada, ':cId' => $id_cliente]);

        return ($stmt->rowCount() > 0);
    }

    /**
     * Auxiliar: Haversine
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
        $rad = M_PI / 180;
        $dLat = ($lat2 - $lat1) * $rad;
        $dLon = ($lon2 - $lon1) * $rad;
        $a = sin($dLat/2) * sin($dLat/2) + cos($lat1 * $rad) * cos($lat2 * $rad) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return 6371000 * $c;
    }
}