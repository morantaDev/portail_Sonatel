<?php
    // session_start();
    #Connexion to de database
    $HOST = "localhost";
    $PORT = "5432";
    $DBNAME = "sms_pro_database";
    $PWD = "Wizzle21#";
    try {
        $dsn = "pgsql:host=$HOST;port=$PORT;dbname=$DBNAME;user=moranta;password=$PWD";
        $db = new PDO($dsn);
    } catch (Exception $e) {
        Echo $e->getMessage();
    }
?>

