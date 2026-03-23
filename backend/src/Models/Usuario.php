<?php

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id_usuario;
    public $nombre;
    public $email;
    public $rol;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function leer() {
        $query = "SELECT id, nombre, email, rol FROM usuarios";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function login($email) {
    $query = "SELECT id_usuario, nombre, password, rol FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->execute();
    return $stmt;
}
}

