<?php

class DatabaseConnection {
    private $host = "localhost";
    private $port = "5432";
    private $username = "moranta";
    private $password = "Wizzle21#";
    private $db_name = "sms_pro_database";
    private $conn;

    public function __construct(){
        try {
            $this->conn = new PDO("pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function query($sql){
        return $this->conn->query($sql);
    }

    public function close(){
        // PDO doesn't have a close method, you can unset the connection
        $this->conn = null;
    }
}

?>
