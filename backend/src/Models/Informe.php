<?php

class Informe {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Función para obtener el historial de visitas de un día concreto cruzando las 4 tablas
    public function obtenerHistorialDiario($fecha) {
        try {
            $query = "SELECT 
                        a.id_asignacion, 
                        u.nombre as empleado, 
                        p.nombre as paciente, 
                        p.direccion,
                        a.estado, 
                        r.hora_inicio, 
                        r.hora_fin, 
                        r.minutos_visita
                      FROM asignaciones a
                      JOIN usuarios u ON a.id_usuario = u.id_usuario
                      JOIN pacientes p ON a.id_paciente = p.id_paciente
                      LEFT JOIN registros_visita r ON a.id_asignacion = r.id_asignacion
                      WHERE a.fecha = :fecha
                      ORDER BY u.nombre ASC, a.orden_visita ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "total_registros" => count($resultados),
                "data" => $resultados
            ];

        } catch(PDOException $e) {
            return [
                "status" => "error", 
                "message" => "Error al obtener el informe: " . $e->getMessage()
            ];
        }
    }
}
?>