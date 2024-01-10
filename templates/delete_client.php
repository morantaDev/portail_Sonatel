<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once "../helpers/database_class.php";
    include "../models/client_class.php";


    try {

        //instance the client model
        $client = new Db_client();
        //connect to the datebase
        $db = new DatabaseConnection();
        $db = $db->getConnection();
    

        if (!empty($_POST) && $_SERVER["REQUEST_METHOD"] === 'POST') {
            $nomClient = trim($_POST["nomClient"]);

            echo $nomClient;

            //Get the client id from the client table
            $sql = "DELETE FROM client WHERE nomclient=?";
            $query = $db->prepare($sql);
            $result = $query->execute([$nomClient]);



            if ($result) {
                echo "Le partenaire a été supprimé avec succès de la table partenaire";
            } else {
                echo "Erreur lors de la tentative de suppression du partenaire de la table correspondante";
            }

            // //Get client name that i want to delete from de database
            $sql = "DELETE FROM billing WHERE client=?";
            $query = $db->prepare($sql);
            $result = $query->execute([$nomClient]);
            if ($result) {
                echo "Le partenaire a été supprimé avec succès de la base de données";
            } else {
                echo "Erreur lors de la tentative de suppression du partenaire de la base de données";
            }
        }
    
    } catch (Exception $e) {
        echo $e->getMessage();
    }



?>