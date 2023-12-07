<?php

    session_start();

    require_once "connexion.php";

    $HOST = "localhost";
    $PORT = "5432";
    $DBNAME = "sms_pro_database";
    $PWD = "Wizzle21#";

    try {
        $dsn = "pgsql:host=$HOST;port=$PORT;dbname=$DBNAME;user=moranta;password=$PWD";
        $db = new PDO($dsn);

        $tablesQuery = [
            "CREATE TABLE IF NOT EXISTS client (
                        id_client SERIAL PRIMARY KEY,
                        nomclient VARCHAR(200) UNIQUE NOT NULL
                    );
            ",
            "CREATE TABLE IF NOT EXISTS catalogue_aggregateur (
                id_catalogue_aggregat SERIAL PRIMARY KEY,
                paliers VARCHAR(200),
                tarif_on_net VARCHAR(200),
                tarif_off_net VARCHAR(200),
                tarif_moyene VARCHAR(200)
            );"
            // , 
            // "CREATE TABLE IF NOT EXISTS type_client (
            //     id_type_client SERIAL PRIMARY KEY,
            //     describ VARCHAR(200)
            // );
            // "
            ,
            "CREATE TABLE IF NOT EXISTS utilisateur (
                id_utilisateur SERIAL PRIMARY KEY,
                email VARCHAR(250) UNIQUE NOT NULL,
                nom_utilisateur VARCHAR(100) UNIQUE NOT NULL,
                mot_passe VARCHAR(250)
            );
            ",
            "  CREATE TABLE IF NOT EXISTS catalogue (
                id_catalogue SERIAL PRIMARY KEY,
                type VARCHAR (50),
                code VARCHAR(200),
                tarif REAL,
                ktck VARCHAR(200)
                -- id_type_client INTEGER, 
                -- FOREIGN KEY (id_type_client) REFERENCES type_client(id_type_client)
            );
            ",
            "   CREATE TABLE IF NOT EXISTS osm (
                id_osm SERIAL PRIMARY KEY,
                compte VARCHAR(200),
                trafic_associe INTEGER,
                -- bagage_inclus BOOLEAN,
                -- nombre_sms INTEGER,
                montant INTEGER
                -- id_catalogue INTEGER,
                -- FOREIGN KEY (id_catalogue) REFERENCES catalogue(id_catalogue)
            );
            ",
            "   CREATE TABLE IF NOT EXISTS billing_aggregateur (
                id_billing_aggregat SERIAL PRIMARY KEY,
                id_utilisateur INTEGER,
                FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
            );
            ",
            "CREATE TABLE IF NOT EXISTS billing (
                id_billing SERIAL PRIMARY KEY,
                client VARCHAR(200),
                compte VARCHAR(200),
                destination VARCHAR(200),
                nombre_sms_mois INTEGER,
                libelle VARCHAR(200),
                mois_fac DATE,
                id_utilisateur INTEGER,
                FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
            );
            ",
            "CREATE TABLE IF NOT EXISTS client_billing (
                id_client_billing SERIAL PRIMARY KEY,
                id_client INTEGER,
                id_billing INTEGER,
                FOREIGN KEY (id_client) REFERENCES client(id_client),
                FOREIGN KEY (id_billing) REFERENCES billing(id_billing)
            );
            ","CREATE TABLE IF NOT EXISTS billing_aggregateur_client (
                id_bac SERIAL PRIMARY KEY,
                id_billing_aggregat INTEGER,
                id_client INTEGER,
                FOREIGN KEY (id_billing_aggregat) REFERENCES billing_aggregateur(id_billing_aggregat),
                FOREIGN KEY (id_client) REFERENCES client(id_client)
            );
            ",
            "CREATE TABLE IF NOT EXISTS nd_numero (
                id_numero SERIAL PRIMARY KEY,
                id_client INTEGER,
                id_osm INTEGER,
                id_catalogue INTEGER,
                indicateur_osm BOOLEAN,
                id_catalogue_aggregat INTEGER,
                FOREIGN KEY (id_client) REFERENCES client(id_client),
                FOREIGN KEY (id_osm) REFERENCES osm(id_osm),
                FOREIGN KEY (id_osm) REFERENCES catalogue(id_catalogue),
                FOREIGN KEY (id_catalogue_aggregat) REFERENCES catalogue_aggregateur(id_catalogue_aggregat)
            );
            "
        ];
        foreach($tablesQuery as $query){
            $db -> exec($query);
        }
        

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']=="POST"){
            $UserName = trim($_REQUEST['UserName']);
            $password = trim($_REQUEST['password']);

            $toLowerUserName = strtolower($UserName);
            // $Hache_Password = password_hash($password);

            #Check if this connected user exists in our database
            $queryUser = $db->prepare("SELECT * FROM utilisateur WHERE (nom_utilisateur=:username OR email=:email) AND mot_passe=:password");
            $queryUser->bindParam(':username', $toLowerUserName, PDO::PARAM_STR);
            $queryUser->bindParam(':email', $toLowerUserName, PDO::PARAM_STR);
            $queryUser->bindParam(':password', $password, PDO::PARAM_STR);
            $queryUser->execute();

            $user = $queryUser->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                die("Cet utilisateur n'existe pas ou le mot de passse n'est pas correct dans la base de données");
                exit();
            } else {
                $_SESSION['UserName'] = $UserName;
                $_SESSION['User_Id'] = $user;

            }
        }

        // Assurez-vous qu'aucune sortie n'est générée avant l'en-tête de redirection
        // ob_start();
        
        header ('Location: with_sidebar_page.php');
        exit();


    } catch (Exception $e) {
        echo $e -> getMessage();
    }

?>