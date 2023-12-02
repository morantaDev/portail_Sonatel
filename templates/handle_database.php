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
                        nomclient VARCHAR(200),
                        prenom_client VARCHAR(200)
                    );
            ",
            "CREATE TABLE IF NOT EXISTS catalogue_aggregateur (
                id_catalogue_aggregat SERIAL PRIMARY KEY,
                paliers VARCHAR(200),
                tarif_on_net VARCHAR(200),
                tarif_off_net VARCHAR(200),
                tarif_moyene VARCHAR(200)
            );", "
            CREATE TABLE IF NOT EXISTS type_client (
                id_type_client SERIAL PRIMARY KEY,
                describ VARCHAR(200)
            );
            ",
            "CREATE TABLE IF NOT EXISTS utilisateur (
                id_utilisateur SERIAL PRIMARY KEY,
                email VARCHAR(250),
                nom_utilisateur VARCHAR(100),
                mot_passe VARCHAR(250)
            );
            ",
            "  CREATE TABLE IF NOT EXISTS catalogue (
                id_catalogue SERIAL PRIMARY KEY,
                indicateur_osm BOOLEAN,
                code VARCHAR(200),
                tarif REAL,
                ktck VARCHAR(200),
                id_type_client INTEGER,
                FOREIGN KEY (id_type_client) REFERENCES type_client(id_type_client)
            );
            ",
            "   CREATE TABLE IF NOT EXISTS offre_sur_mesure (
                id_oms SERIAL PRIMARY KEY,
                description VARCHAR(200),
                tarif_associe INTEGER,
                montant_total INTEGER,
                bagage_inclus BOOLEAN,
                nombre_sms INTEGER,
                id_catalogue INTEGER,
                FOREIGN KEY (id_catalogue) REFERENCES catalogue(id_catalogue)
            );
            ",
            "   CREATE TABLE IF NOT EXISTS biling_aggregateur (
                id_biling_aggregat SERIAL PRIMARY KEY,
                id_utilisateur INTEGER,
                FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
            );
            ",
            "CREATE TABLE IF NOT EXISTS biling (
                id_biling SERIAL PRIMARY KEY,
                nombre_sms_mois INTEGER,
                libelle VARCHAR(200),
                destination VARCHAR(200),
                mois_fac DATE,
                id_utilisateur INTEGER,
                FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
            );
            ",
            "CREATE TABLE IF NOT EXISTS client_biling (
                id_client_biling SERIAL PRIMARY KEY,
                id_client INTEGER,
                id_biling INTEGER,
                FOREIGN KEY (id_client) REFERENCES client(id_client),
                FOREIGN KEY (id_biling) REFERENCES biling(id_biling)
            );
            ","CREATE TABLE IF NOT EXISTS biling_aggregateur_client (
                id_bac SERIAL PRIMARY KEY,
                id_biling_aggregat INTEGER,
                id_client INTEGER,
                FOREIGN KEY (id_biling_aggregat) REFERENCES biling_aggregateur(id_biling_aggregat),
                FOREIGN KEY (id_client) REFERENCES client(id_client)
            );
            ",
            "CREATE TABLE IF NOT EXISTS nd_numero (
                id_numero SERIAL PRIMARY KEY,
                id_client INTEGER,
                id_oms INTEGER,
                id_catalogue_aggregat INTEGER,
                FOREIGN KEY (id_client) REFERENCES client(id_client),
                FOREIGN KEY (id_oms) REFERENCES offre_sur_mesure(id_oms),
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
            } else {
                $_SESSION['UserName'] = $UserName;
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