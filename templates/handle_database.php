<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    session_start();

    require_once "connexion.php";
    include "flash.php";
    require_once "../helpers/database_class.php";

    try {

        $db = new DatabaseConnection();
        $db = $db->getConnection();

        $tablesQuery = [
            "CREATE TABLE IF NOT EXISTS client (
                        id_client SERIAL PRIMARY KEY,
                        nomclient VARCHAR(200) UNIQUE NOT NULL
                    );
            ",
            "CREATE TABLE IF NOT EXISTS catalogue_aggregateur (
                id_catalogue_aggregat SERIAL PRIMARY KEY,
                paliers VARCHAR(200),
                tarif_on_net DECIMAL(5, 2),
                tarif_off_net DECIMAL(5, 2),
                tarif_moyen DECIMAL(5,2)
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
            ",
            "CREATE TABLE IF NOT EXISTS archive_ticket (
                id_fichier SERIAL PRIMARY KEY NOT NULL,
                nom_fichier VARCHAR(100) NOT NULL,
                chemin_fichier VARCHAR(250) NULL,
                date_creation timestamp default NULL
                );",
                "CREATE TABLE IF NOT EXISTS donnees_tickets (
                    id_ticket SERIAL PRIMARY KEY NOT NULL,
                    id_fichier INT,
                    Compte VARCHAR(10) NOT NULL,
                    NTICKET VARCHAR(10) NOT NULL,
                    CPROD VARCHAR(10) NOT NULL,
                    TYPE_TCK VARCHAR(10) NOT NULL,
                    DATOP_TCK DATE NOT NULL,
                    SENS VARCHAR(255) NOT NULL,
                    MTN_TCK INT NOT NULL,
                    KTCK VARCHAR(255) NOT NULL,
                    FOREIGN KEY (id_fichier) REFERENCES archive_ticket(id_fichier)
                );"
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
                // die("Cet utilisateur n'existe pas ou le mot de passse n'est pas correct dans la base de données");
                header ('Location: index.php');

                flash('error_login','Cet utilisateur n\'existe pas ou le mot de passse n\'est pas correct dans la base de données', 'red');

                exit();
            } else {
                $_SESSION['UserName'] = $UserName;
                $_SESSION['User'] = $user;
                flash('login','Utilisateur connecté avec succès');

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
