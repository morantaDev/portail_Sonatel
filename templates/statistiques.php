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
        };
        
    } catch (Exception $e) {
        echo $e->getMessage();
    }


?>