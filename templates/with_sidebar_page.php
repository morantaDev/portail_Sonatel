<?php
    session_start();

    include "../models/client_class.php";
    include "flash.php";

    $db = new DatabaseConnection();
    $db = $db->getConnection();

    $sql = "SELECT * FROM archive_ticket";
    $files = $db->prepare($sql);
    $files->execute();
    $resultsFiles = $files->fetchAll(PDO::FETCH_ASSOC);

    // Instanciez la classe Db_client
    $dbClient = new Db_client();
    // Obtenez tous les clients
    $clients = $dbClient->getAllClients();

    // Nombre d'éléments par page
    $perPage = 15;

    // Nombre total de pages
    $totalPages = ceil(count($clients) / $perPage);

    // Page actuelle (par défaut à la première page si non définie)
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

    // Sélectionnez les clients pour la page actuelle
    $start = ($currentPage - 1) * $perPage;
    $end = $start + $perPage;
    $selectedClients = array_slice($clients, $start, $perPage);


    
    // Vérifier si UserName est présent dans la session
    if (!isset($_SESSION['UserName'])) {
        // Rediriger vers la page de connexion si UserName n'est pas présent
        header("Location: index.php");
        exit();
    }

    // Si UserName est présent, récupérez la valeur
    $UserName = $_SESSION['UserName'];


    //Get all informations from the historical table
    $HistoSql = "SELECT * FROM historique";
    $Historiques = $db->prepare($HistoSql);
    $Historiques->execute();
    $AllHistoriques = $Historiques->fetchAll(PDO::FETCH_ASSOC);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<title>Main page</title>
<style>
    body {
        margin: 0;
        width: 100%;
        height: 100vh;
        background-color: none; /* Couleur de fond de la page */
        /*margin: 20px;  Supprime la marge par défaut du body */
        font-family: Arial, sans-serif; /* Police de caractères */
    }
    .full_container{
        /* border: 1px solid black; */
        /* position: relative; */
        width: 100%;
        height: 100vh;
        margin-top: 79px;
        z-index: 2;
    }
    .main_container{
        /* border: 1px solid black; */
        width: 100%;
        height: 100%;
        display: flex;
    }
    .sidebar{
        height: 100vh;
        width: 250px;
        min-width: 250px;
        max-width: 250px;
        background: rgb(220,220,220);
        margin: 0;
        padding: 0;
        position: relative;
        top: 78px;
        /* z-index: 2; */
        left: -250px;
        position: absolute;
        transition: left 0.3s;
    }
    .content{
        width: 100%;
        padding-left: 2%;
        background-image: url('../assets/sonatel_republique.jpeg');
        background-repeat: no-repeat;
        background-size: 100%;
    }
    .sidebar h1{
        text-align: center;
        background-color: rgb(105,105,105);
        padding: 20px 0;
        position: relative;
        top: -2px;
        font-size: 25px;
        margin-top: 3px;
    }
    .menu_list{
        width: 100%;
    }
    .menu_item {
        position: relative;
        right: 40px;
        list-style: none;
        text-align: center;
        margin: 20px;
        padding: 20px;
        font-size: 17px;
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        width: 100%;
        font-weight: bold;
    }
    .menu_item:hover{
        cursor: pointer;
        background-color: 	rgb(105,105,105);
        color: white;
        font-size: 18px;
        padding: 10%;
        margin: 0;
        width: 250px;
    }
    .open .content{
        width:  100%;
    }
    .content.collapsed {
        width: calc(100% - 250px); /* Ajustez la largeur en fonction de la largeur du sidebar ouvert */
        left: 250px;
        margin-left: 250px;
        /* transition: left 0.3s; */

    }
    .open {
        left: 0; /* Ajustez la position lorsque le sidebar est ouvert */
    }
    .footer p {
        text-align: center;
        color: white;
    }
    .footer{
        /* border: 1px solid black; */
        height: 10%;
        width: 100%;
        position: absolute;/* Met le pied de page en bas de son contenant */ 
        background-color: black;
    }
    .footer p { 
        padding-top: 30px;
    }
    #list_button svg {
        color: white;
        position: relative;
        top: -10%;
        background-color:	rgb(105,105,105);
        left: 227px;
        padding: 10px 5px 10px 20px;
        border-radius: 50%;
    }
    .logo img{
        margin-top: 10px;
    }
    .header-container{
        width: 100%; 
        height: 79px; 
        left: 0px; 
        top: 0px; 
        position: fixed; 
        background: black;
        z-index: 2;
    }
    .search{
        padding: 15px;
        position: relative;
        left: 30%;
        display: flex;
    }
    .search input{
        font-size: 20px;
        background-color: transparent;
        /* border: 1px solid white; */
        color: white;
        /* padding-left: 6px; */
        padding-top: 1px;
        border-radius: 5px 0 0 5px;
        padding: 2px 5px;
    }
    .search input{
        border: none;
        /* color: white; */
        color: #492809;
        border-bottom: 1px solid white;
    }
    .search_input::placeholder{
        color: white;
        opacity: 1;
    }
    .search_input .logo img{
        width: 220px;
        height: 50px;
        padding: 12px 20px;
        /* border-radius: 30%; */
    }
    .connectUser{
        position: relative;
        /* border: 1px solid black; */
        left: 50%;
        display: flex;
        width: 15%;
        top: -2px;
        padding-bottom: 4px;
        height: 100%;
    }
    .connectedLogo{
        margin-top: 5px;
        padding:  10px;
        background-color: black;
        border-radius: 50%;
        color: white;
    }
    .userName{
        color: white;
        margin-left: 10px;
        padding-top: 30px;
        font-size: 17px;
        width: 20%;
        /* border: 1px solid black; */
        margin-top: -6%;
    }

    .userName a{
        color: red;
    }
    .userName span{
        color: white;
        font-size: 20px;
    }
    .getFiles{
        display: none;
    }
    .table-client {
        display: none;
    }
    .table-fichiersTraites{
        display: none;
    }
    .table-fichiersTraites table{
        background-color: #fff;
        border: none;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        width: 100%;
    }
    .table-fichiersTraites{
        margin: 0 auto;
        margin-top: 20px;
        overflow-x: auto;
        /* margin-right: 0%; */
        width: 93%; 
        margin-left: 40px;
        /* transition: left 0s; */
    }
    .table-fichiersTraites th{
        background-color: rgb(255,140,0);
        color: #fff;
        text-align: center;
        font-size: 18px;
    }
    .table-fichiersTraites table:hover {
        transform: scale(1.02);
    }
    #search-addon{
        background-color: black;
    }
    .table-client {
        margin: 0 auto; /* Ajoutez cette ligne pour centrer le tableau horizontalement */
        margin-top: 20px;
        overflow-x: auto;
        margin-left: 15%;
        width: 90%; /* Ajustez la largeur du tableau selon vos besoins */
    }

    .table-client table, .table-Historiques table {
        background-color: #fff;
        border: none;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        width: 100%;
    }

    .table-client table:hover, .table-Historiques table:hover {
        transform: scale(1.02);
    }

    .table-client th, .table-Historiques th {
        background-color: rgb(255,140,0);
        color: #fff;
        text-align: center;
        font-size: 18px;
    }
    .table-client th  i{
        font-size: 18px;
        margin-right: 10px;
    }
    .table-client h2{
        font-size: 32px;
        margin-right: 10px;
    }

    .table-client td:nth-child(1) {
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
    }
    .table-client td:nth-child(3) i {
        vertical-align: middle;
        margin-left: 10px;
        
    }
    .table-client td:nth-child(3) i:hover {
        cursor: pointer;
    }
    .pagination{
        margin-left: 25%;
    }
    .page-link{
        color: #492809;

    }
    tbody{
        text-align: center;
    }
    .table-Historiques, .table-statistiques{
        display: none;
    }
    .table-statistiques button{
        color: red;
    }
    
</style>
</head>
<body>
<!-- <div id="header"></div> -->
<div class="header-container">
    <div class="search_input input-group rounded" style="display: flex;">
        <div class="logo">
            <img src="../assets/sonatel_orange_sans_fond.png" alt="">
        </div>
        <div class="search">
            <input type="search" class="form-control rounded-left" placeholder="Recherche" aria-label="Search" aria-describedby="search-addon" />
            <span class="input-group-text border-0" id="search-addon">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16" style="color: white;">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                </svg>
            </span>
        </div>

        <!-- User connect -->
        <div class="connectUser">
            <div class="connectedLogo">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"/>
            </svg>
            </div>
            <div class="userName">
                <span><?php echo $UserName ;?></span>
                <a href="logout_page.php">Déconnexion</a>
            </div>
        </div>
    </div>
</div>
<div class="full_container">

    <div class="main_container">
        <!-- Ajouter le Header ici -->

        <div class="sidebar">
            <!-- <button class="btn btn-primary">FERMER</button> -->
            <h1>Tableau de bord</h1>
            <ul class="menu_list">
                <li class="menu_item">Accueil</li>
                <li class="menu_item">Gestion Partenaires</li>
                <li class="menu_item">Gestion des fichiers</li>
                <li class="menu_item">Fichiers traités</li>
                <li class="menu_item">Historiques</li>
            </ul>
            <span id="list_button">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
            </svg>
            </span>
        </div>
        <div class="content">
            <p><?php echo flash('login')?></p>
            <div class="getFiles">
                <?php include "saly.php"; ?>
            </div>

            <!-- This section is used to display all users -->
            <div class="col-md-10">
                <div class="table-client" id="table-client">
                    <table class="table table-bordered">
                        <h2>Liste des partenaires</h2>
                        <thead>
                            <tr>
                                <th class="compte">COMPTE</th>
                                <th class="nom_client"><i class="bi bi-person"></i>PARTENAIRE</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($selectedClients as $client): ?>
                                <tr>
                                    <td><?php echo $client['compte']; ?></td>
                                    <td><strong><?php echo strtoupper($client['nomclient']);?></strong></td>
                                    <td style="display:flex;" data-class="<?php echo $client['nomclient']; ?>"><i class="bi bi-eye show_stat" style="" title="voir stat"></i><i class="bi bi-pencil-square edit_client" title ="modifier partenaire"></i><i class="bi bi-save save_client" title="enregistrer modif"></i><i class="bi bi-trash3-fill delete_client" title="supprimer partenaire"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Ajouter une pagination -->
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $currentPage) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
            
            <!-- Add some section: this one is for displying all treated files -->
            <div class="col-md-12">
                <div class="table-fichiersTraites">
                    <table class="table table-bordered">
                        <h2>Liste des fichiers traités</h2>
                        <thead>
                            <tr>
                                <th class="id_fichier">Id Fichiers</th>
                                <th class="nom_fichier"><i class="bi bi-file-earmark"></i>Nom du fichier</th>
                                <th class="chemin_fichier"><i class="bi bi-filetype-xlsx"></i>Chemin du fichier</th>
                                <th class="date_creation"><i class="bi bi-calendar-event"></i>Date de création du fichier</th>
                                <th class="action_fichier">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count=0;foreach ($resultsFiles as $file): ?>
                                <tr>
                                    <td class="id_fichier"><strong><?php echo $file['id_fichier'] ?></strong></td>
                                    <td><strong><?php echo $file['nom_fichier'] ?></strong></td>
                                    <td><strong><?php echo $file['chemin_fichier']?></strong></td>
                                    <td><strong><?php echo $file['date_creation']?></strong></td>
                                    <td id="<?php echo $count ?>"><i class="bi bi-download download_file" style="cursor: pointer; font-size: 25px;"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfooter>
                            <p><?php echo flash('success_download') ?></p>
                        </tfooter>
                    </table>
                </div>
            </div>


            <!-- Add some section: this one is for displying all historical informations of our appli -->
            <div class="col-md-12">
                <div class="table-Historiques">
                    <table class="table table-bordered">
                        <h2>Historiques</h2>
                        <thead>
                            <tr>
                                <th class="id_historiqque">Action</th>
                                <th class="date_creation"><i class="bi bi-calendar-event"></i>Date de création</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count=0;foreach ($AllHistoriques as $historique): ?>
                                <tr>
                                    <td><strong><?php echo $historique['action'] ?></strong></td>
                                    <td><strong><?php echo $historique['date_creation']?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfooter>
                            <?php echo flash('error_download') ?>
                        </tfooter>
                    </table>
                </div>
            </div>

            <!-- Display statistics of any client here -->
            <div class="col-md-12">
                <div class="table-statistiques">
                    <button style="padding: 7px 25px; font-size: 20px; border-radius: 10%; color: black; background-color: rgb(255,140,0); font-weight: bold;"><i class="bi bi-arrow-left"></i>RETOUR</button>

                    <!-- Search any user compte stat and display it -->
                    <div class="row">
                        <div class="col-sm-12" align="center">
                            <p style="font-size: 30px; padding-bottom: 20px"><input type="text" id="datepicker" style="margin-right:15px;" placeholder="Choisir une date"><button style="background-color: black; color: white; margin-left: -20px; margin-top: 10%;">Recherche</button></p>
                        </div>            
                    </div>

                <div class="full_stat" style="background-color: black; width: 97%; height: 100%;">
                    <h2 style="color: white; text-align: center; padding-top: 30px; font-size: 40px">Statistiques</h2>
                    
                    <h4 style="color: white; padding-bottom: 20px; padding-left: 30px;">Numéro de compte: <Strong class="numeroCompte">WTS02543</Strong></h4>
                    
                    <h5 style="color: white; padding-bottom: 20px; padding-left: 30px;">Date du ticket: <strong>31 Décembre 2023</strong></h4>
                    <!-- Display all statistics here -->
                    <div class="stat_content" style="display: flex; align-content: center; width: 100%; border: 1px solid red; padding-left: 10%">
                        <div class="card text-bg-secondary mb-3" style="max-width: 18rem; margin-right: 5%;">
                            <div class="card-header">Nombre SMS National</div>
                            <div class="card-body">
                                <h5 class="card-title_national" style="text-align: center; font-size: 40px;">345</h5>
                                <!-- <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p> -->
                            </div>
                        </div>
                        <div class="card text-bg-success mb-3" style="max-width: 18rem; margin-right: 5%">
                            <div class="card-header">Nombre SMS international</div>
                            <div class="card-body">
                                <h5 class="card-title_international" style="text-align: center; font-size: 40px;">3456</h5>
                            </div>
                        </div>
                        <div class="card text-bg-danger mb-3" style="max-width: 18rem; margin-right: 5%">
                        <div class="card-header">Montant Ticket National</div>
                        <div class="card-body">
                            <h5 class="card-title_montantNat" style="text-align: center; font-size: 40px;">1320000</h5>
                        </div>
                        </div>
                        <div class="card text-bg-danger mb-3" style="max-width: 18rem; margin-right: 5%">
                        <div class="card-header">Montant Ticket International</div>
                        <div class="card-body">
                            <h5 class="card-title_montantInt" style="text-align: center; font-size: 40px;">1320000</h5>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>

            </div>


        </div>
    </div>
</div>
<div class="footer">
    <p>&copy;copyright moranta&Salimata 2023</p>
</div>


<script>
    $(document).ready(function (e) {
        console.log("Chargement de la page terminé.");

        // Variable pour stocker l'élément sélectionné
        var selectedMenuItem = "";

        $("#list_button").on('click', function () {
            $(".sidebar").toggleClass("open");
            $(".content").toggleClass("collapsed");
            $("#list_button").toggleClass("open");
        });

        $(".menu_list li").click(function () {
            var selected_item = $(this).text().trim(); // Supprimer les espaces indésirables
            console.log(selected_item);

            // Afficher ou masquer les éléments en fonction de l'élément sélectionné
            if (selected_item === 'Gestion des fichiers') {
                $(".getFiles").show();
                $(".table-client").hide(); // Masquer le tableau des clients
                $(".table-fichiersTraites").hide(); //Masquer la table des fichiers traités
                $(".table-Historiques").hide(); //Masquer la table des historiques 
            } else if (selected_item === 'Gestion Partenaires') {
                $(".getFiles").hide();
                $(".table-client").show(); // Afficher la table des clients
                $(".table-fichiersTraites").hide(); //Masquer la table des fichiers traités
                $(".table-Historiques").hide(); //Masquer la table des historiques 
            } else if (selected_item === 'Fichiers traités') {
                $(".getFiles").hide();
                $(".table-client").hide(); // Afficher la table des clients
                $(".table-fichiersTraites").show(); //Masquer la table des fichiers traités
                $(".table-Historiques").hide(); //Masquer la table des historiques 
            } else if (selected_item === 'Historiques'){
                $(".getFiles").hide();
                $(".table-client").hide(); // Afficher la table des clients
                $(".table-fichiersTraites").hide(); //Masquer la table des fichiers traités        }
                $(".table-Historiques").show(); //Masquer la table des historiques 
            }else {
                $(".getFiles").hide();
                $(".table-client").hide(); // Afficher la table des clients
                $(".table-fichiersTraites").hide(); //Masquer la table des fichiers traités        }
                $(".table-Historiques").hide(); //Masquer la table des historiques 
            }
        });

        // Gérer la pagination
        $(document).on('click', '.table-client .pagination a.page-link', function (e) {
            e.preventDefault();
            // Charger la page suivante via AJAX
            var page = $(this).attr("href").split("=")[1];
            loadPage(page);
        });

        function loadPage(page) {
            // Effectuer une requête AJAX pour charger le contenu du tableau
            console.log('Chargement de la page', page);

            $.ajax({
                url: 'charger_page_clients.php?page=' + page,
                type: 'GET',
                success: function (response) {
                    // Mettre à jour le contenu de la div avec le nouveau HTML
                    $(".table-client").html(response);
                    console.log(response);
                },
                error: function () {
                    console.log('Erreur lors du chargement de la page');
                }
            });
        }

            setTimeout(function() {
                $('#flash-message').fadeOut('slow');
            }, 2000); // 5000 millisecondes (5 secondes)

            

        $(".download_file").on('click', function(){
            // var download_item = $(this).attr("id");
            var row = $(this).closest("tr"); // Récupérer la ligne parente
            var idFichier = row.find("td:eq(0)").text();;

            console.time('clickHandler');
            alert(idFichier);

            $.ajax({
                url: 'generate_Ticket_SMSPlus.php',
                type: 'POST',
                // dataType: 'json',
                data: { idFichier: idFichier },
                success: function (response) {
                    // Mettre à jour le contenu de la div avec le nouveau HTML
                    alert(response);
                    $('.table-Historiques tbody').empty();

                    response.forEach(function (historique) {
                        $('.table-Historiques tbody').append(
                            '<tr>' +
                            '<td><strong>' + historique.action + '</strong></td>' +
                            '<td><strong>' + historique.date_creation + '</strong></td>' +
                            '</tr>'
                        );
                    });
                    // // Actualiser la page après la mise à jour
                    // location.reload();

                    console.timeEnd('clickHandler');
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log('Erreur lors de la requête AJAX : ' + textStatus + ' ' + errorThrown);
                    console.log(xhr.responseText); // Afficher la réponse exacte du serveur
                    console.timeEnd('clickHandler');
                }
            });

        })

        $(".edit_client").on('click', function(){
            var row = $(this).closest("tr"); // Récupérer la ligne parente

            // Rendre les cellules de la ligne éditables
            row.find("td").attr("contenteditable", "true");

            // Ajouter une classe pour styliser les cellules éditables
            row.find("td").addClass("editable");

            // Désactiver le bouton "Éditer" pour éviter les conflits
            $(this).prop("disabled", true);

            // Activer le bouton "Enregistrer" pour capturer les modifications
            row.find(".save_client").prop("disabled", false);

                // Récupérer l'ancien nom du client
            var ancienNomClient = row.find("td:eq(1)").text();
            row.data("ancien-nom", ancienNomClient);
        });

        $(".save_client").on('click', function(){
            alert("voulez-vous sauvegarder les modifications?");
            var row = $(this).closest("tr"); // Récupérer la ligne parente

            // Récupérer les valeurs modifiées dans chaque cellule
            var idClient = row.find("td:eq(0)").text();
            var newNomClient = row.find("td:eq(1)").text();
            var ancienNomClient = row.data("ancien-nom");

            alert(idClient);
            alert(newNomClient);

            // Effectuer une requête AJAX pour enregistrer les modifications
            $.ajax({
                url: "edit_client.php",
                type: "POST",
                data: {idClient: idClient, ancienNomClient : ancienNomClient, newNomClient: newNomClient},
                success: function(response){
                    // console.log("Les modifications ont été enregistrées avec succès");
                    alert(response);
                },
                error: function(){
                    console.log('Erreur lors de la tentative d\'enregistrement des modifications');
                }
            });

            // Rendre les cellules de la ligne non éditables
            row.find("td").removeAttr("contenteditable");

            // Supprimer la classe "editable" des cellules
            row.find("td").removeClass("editable");

            // Désactiver le bouton "Enregistrer"
            $(this).prop("disabled", true);

            // Réactiver le bouton "Éditer"
            row.find(".edit_client").prop("disabled", false);
        });




        //Delete client from de database
        $(".delete_client").on('click', function(){
        var nomClient = $(this).parent().data("class");
        alert(nomClient);
        $.ajax({
                url:"delete_client.php",
                type: "POST",
                data: {nomClient: nomClient},
                success: function(response){
                    alert(response);
                },
                error: function(){
                    console.log('Erreur lors de la tentative de suppression du client.');
                }
            });
        });
        
        $(".show_stat").on('click', function(){
            $(".table-client").hide();
            $(".table-statistiques").show();
            var row = $(this).closest("tr"); // Récupérer la ligne parente
            var compte = row.find("td:eq(0)").text();


            $('.numeroCompte').html(compte);

            alert(compte);
            $.ajax({
                url: "statistiques.php",
                type: "POST",
                dataType: 'json',
                data: {compte: compte},
                success: function(response){
                    // alert(response);
                    alert(response[1]);
                    if(response.length > 1){
                        for(let i=0; i < response.length; i++){
                            if(i===0){
                                var montant = response[0]["mtn_tck"].match(/\d+/g);
                                var ktck = response[0]["ktck"].match(/\d+/g);
                                console.log(montant);
                                $(".card-body .card-title_international").html(montant);
                                $('.card-body .card-title_montantInt').html(montant);
    
                            }else if(i===1){
                                var ktck = response[1]["ktck"].match(/\d+/g);
                                var montant = response[1]["mtn_tck"].match(/\d+/g);
                                $(".card-body .card-title_national").html(ktck);
                                $('.card-body .card-title_montantNat').html(montant);
                            } else {
                                $(".card-body .card-title_national").html(0);
                                $(".card-body .card-title_international").html(0);
                                $('.card-body .card-title_montantNat').html(0);
                                $('.card-body .card-title_montantInt').html(0);
    
                            }
                        }
                    }else{
                        var ktck = response[0]["ktck"].match(/\d+/g);
                        var montant = response[0]["mtn_tck"].match(/\d+/g);
                        $(".card-body .card-title_national").html(ktck);
                            $(".card-body .card-title_international").html(0);
                            $('.card-body .card-title_montantNat').html(montant);
                            $('.card-body .card-title_montantInt').html(0);
                    }
                },
                error: function(){
                    console.log("Erreur lors d'une tentative d'affichage de la page statistique");
                }
            });
        });

        //});

        $(".table-statistiques button").on('click', function(){
            $(".table-statistiques").hide();
            $(".table-client").show();
        });

        $(".card-body .card-title_national").on('click', function(){
            $(".card-body .card-title_national").html(0);
        })

        // Data Picker Initialization
        $(function(){
            $("#datepicker").datepicker({
                showButtonPanel: true
            });       
        });

    });


</script>
</body>
</html>