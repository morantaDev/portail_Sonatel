<?php

class Db_client {
    private $db;

    public function __construct(){
        $this->$db = new DatabaseConnection;
    }

    public function get_by_id($id){
        $sql = "SELECT * FROM client WHERE id=$id";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }
    public function getAllClients(){
        $sql = "SELECT * FROM client";
        $result = $this->$db->query($sql);
        return $result->fetch_assoc();
    }
    public function deleteClient($id){
        $query = "SELECT * FROM client WHERE id=$id";
        $getClient = $this->$db->query($query);
        if (!$getClient){
            die ("le client que vous voulez supprimer n'existe pas dans la base de données");
        } else {
            $sql = "DELETE FROM client WHERE id=$id";
            $result = $this->$db->query($sql);
        }
        return $result->fetch_assoc();
    }
    public function updateClient($inpurData, $id){
        $query = "SELECT * FROM client WHERE id=$id";
        $getClient = $this->$db->query($query);
        if (!$getClient){
            die ("le client que vous voulez mettre à jour n'existe pas dans la base de données");
        } else {
            // $sql = "UPDATE client SET  WHERE id=$id";
            // $result = $this->$db->query($sql);
            echo "cette methode n'est pas encore prise en charge";
        }
        return $result->fetch_assoc();
    }
}

?>