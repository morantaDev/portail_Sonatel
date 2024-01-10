<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    require_once "../helpers/database_class.php";

    try {
        // Connect to the database
        $db = new DatabaseConnection();
        $db = $db->getConnection();

        if (!empty($_POST) && $_SERVER["REQUEST_METHOD"] === 'POST') {
            $idClient = (int) trim($_POST["idClient"]);
            $nouveauNomClient = trim($_POST["newNomClient"]);
            $ancienNomClient = trim($_POST["ancienNomClient"]);

            echo $idClient;
            echo $ancienNomClient;
            echo $nouveauNomClient;

            // Update the client in the client table
            $sql = "UPDATE client SET nomclient=? WHERE Id_client=?";
            $query = $db->prepare($sql);
            $stmt = $query->execute([$nouveauNomClient, $idClient]);

            //Apply the same update into the billing table
            $sql = "UPDATE billing SET client=? WHERE client=?";
            $query = $db->prepare($sql);
            $stmt = $query->execute([$nouveauNomClient, $ancienNomClient]);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
?>

