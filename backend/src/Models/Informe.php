<?php
class Informe {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerHistorialDiario($fecha) {
        try {
            // Adaptado a la nueva DB: jornadas, visitas, usuarios y clientes
            $query = "SELECT 
                        v.id_jornada, 
                        u.nombre as empleado, 
                        c.nombre as cliente, 
                        c.direccion,
                        j.estado as estado_jornada, 
                        v.llegada as hora_inicio, 
                        v.salida as hora_fin, 
                        v.duracion_minutos
                      FROM visitas v
                      JOIN jornadas j ON v.id_jornada = j.id
                      JOIN usuarios u ON j.id_usuario = u.id
                      JOIN clientes c ON v.id_cliente = c.id
                      WHERE j.fecha = :fecha
                      ORDER BY u.nombre ASC, v.orden ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":fecha", $fecha);
            $stmt->execute();

            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                "status" => "success",
                "fecha_informe" => $fecha,
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