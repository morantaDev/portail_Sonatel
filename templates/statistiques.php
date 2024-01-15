<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require_once "../helpers/database_class.php";
    include "../models/client_class.php";

    try {
        // Instance the client model
        $client = new Db_client();
        // Connect to the database
        $db = new DatabaseConnection();
        $db = $db->getConnection();

        if (!empty($_POST) && $_SERVER["REQUEST_METHOD"] === 'POST') {
            $compte = trim($_POST["compte"]);
            $date = trim($_POST["date"]);

            $sql = "SELECT * FROM donnees_tickets WHERE compte = ? AND datop_tck = ? LIMIT 2";

            $query = $db->prepare($sql);
            $query->execute([$compte, $date]);
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            // print_r($result);

            // Répondre avec les historiques au format JSON
            header('Content-Type: application/json');
            $json_result = json_encode($result);
            echo $json_result;
        }
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
?>
