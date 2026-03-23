<?php
class Database {
    private $host = "localhost";
    private $db_name = "timeroute"; 
    private $username = "root";     
    private $password = "";        
    public $conn;

    public $id_usuario_sesion = 1; // Para pruebas, simulamos que somos el ID 1

    public function getConnection(){
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>