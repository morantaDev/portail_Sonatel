<?php
    #Connexion to de database
    $HOST = "localhost";
    $PORT = "5432";
    $DBNAME = "sms_pro_database";
    $PASSWORD = "";
    try {
        $connect = pg_connect("host=localhost port=5432 dbname=$ password=''");
        echo "connexion établie";
        header ("Location : with_sidebar_page.html")

    } catch (Exception $e) {
        Echo $e->getMessage();
    }
?>