<?php

    // require_once "../templates/connexion.php";

    // define('DB_HOST','localhost');
    // define('DB_USER','moranta');
    // define('DB_PORT','5432');
    // define('DB_PASSWORD','Wizzle21#');
    // define('DB_DATABASE','sms_pro_database');

// class DatabaseConnection {
//     private $host  = "localhost";
//     private $port = "5432";
//     private $username = "moranta";
//     private $password = "Wizzle21#";
//     private $db_name = "sms_pro_database";

//     public function __constuct(){
//         $this->$conn = new PDO($this->$host, $this->username, $this->$db_name, $this->$port, $this->$password);
//         if ($this->conn->connect_error) {
//             die("Connection failed: " .$this->conn->connect_error);
//         }
//     }

//     public function query($sql){
//         return $this->conn->query($sql);
//     }

//     public function close(){
//         return $this->conn->close();
//     }

// }



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