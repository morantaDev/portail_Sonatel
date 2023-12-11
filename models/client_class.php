<?php

require_once "../helpers/database_class.php";

class Db_client {
    private $db;

    public function __construct(){
        $this->db = new DatabaseConnection;
    }

    public function get_by_id($id){
        $sql = "SELECT * FROM client WHERE id=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllClients(){
        $sql = "SELECT * FROM client";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteClient($id){
        $query = "SELECT * FROM client WHERE id=?";
        $getClient = $this->db->prepare($query);
        $getClient->execute([$id]);

        if (!$getClient->fetch(PDO::FETCH_ASSOC)){
            die("Le client que vous voulez supprimer n'existe pas dans la base de données");
        } else {
            $sql = "DELETE FROM client WHERE id=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateClient($inputData, $id){
        $query = "SELECT * FROM client WHERE id=?";
        $getClient = $this->db->prepare($query);
        $getClient->execute([$id]);

        if (!$getClient->fetch(PDO::FETCH_ASSOC)){
            die("Le client que vous voulez mettre à jour n'existe pas dans la base de données");
        } else {
            // La mise à jour n'est pas encore prise en charge
            return false;
        }
    }
}

?>