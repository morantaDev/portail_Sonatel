<?php
    include "../models/client_class.php";

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


    session_start();
    
    // Vérifier si UserName est présent dans la session
    if (!isset($_SESSION['UserName'])) {
        // Rediriger vers la page de connexion si UserName n'est pas présent
        header("Location: index.html");
        exit();
    }

    // Si UserName est présent, récupérez la valeur
    $UserName = $_SESSION['UserName'];

    
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
<title>Main page</title>
<style>
    body {
        margin: 0;
        width: 100%;
        height: 100%;
    }
    .full_container{
        /* border: 1px solid black; */
        width: 100%;
        height: 100vh;
        margin-top: 79px;
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
        background: #DEC1A7;
        margin: 0;
        padding: 0;
        position: relative;
        top: 78px;
        z-index: 1;
        left: -250px;
        position: absolute;
        transition: left 0.3s;
    }
    .content{
        width: 100%;
        padding-left: 2%;

    }
    .sidebar h1{
        text-align: center;
        background-color: #caa27f;
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
        background-color: #492809;
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
        background-color:#492809;
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
        position: absolute; 
        background: #492809;
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
        margin-left: 5%;
        width: 90%; 
    }
    .table-fichiersTraites th{
        background-color: #492809;
        color: #fff;
        text-align: center;
        font-size: 18px;
    }
    .table-fichiersTraites table:hover {
        transform: scale(1.02);
    }
    #search-addon{
        background-color: #492809;
    }
    .table-client {
        margin: 0 auto; /* Ajoutez cette ligne pour centrer le tableau horizontalement */
        margin-top: 20px;
        overflow-x: auto;
        margin-left: 15%;
        width: 90%; /* Ajustez la largeur du tableau selon vos besoins */
    }

    .table-client table {
        background-color: #fff;
        border: none;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        width: 100%;
    }

    .table-client table:hover {
        transform: scale(1.02);
    }

    .table-client th {
        background-color: #492809;
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
                <li class="menu_item">Traitements</li>
                <li class="menu_item">Historiques</li>
            </ul>
            <span id="list_button">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
            </svg>
            </span>
        </div>
        <div class="content">
            <!-- Toutes les tableaux seront affichés içi -->
            <p><?php echo "Bienvenue" ." ".$UserName ;?></p>
            <div class="getFiles">
                <?php include "saly.html"; ?>
            </div>

            <!-- This section is used to display all users -->
            <div class="col-md-10">
                <div class="table-client">
                    <table class="table table-bordered">
                        <h2>Liste des partenaires</h2>
                        <thead>
                            <tr>
                                <th class="id_client">Id Partenaire</th>
                                <th class="nom_client"><i class="bi bi-person"></i>PARTENAIRE</th>
                                <th class="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($selectedClients as $client): ?>
                                <tr>
                                    <td><?php echo $client['id_client']; ?></td>
                                    <td><strong><?php echo strtoupper($client['nomclient']);?></strong></td>
                                    <td style="display:flex;"><i class="bi bi-pencil-square"></i><i class="bi bi-trash3-fill"></i></td>
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
                            <?php foreach ($resultsFiles as $file): ?>
                                <tr>
                                    <td><?php echo $file['id_fichier'] ?></td>
                                    <td><?php echo $file['nom_fichier'] ?>  </td>
                                    <td><?php echo $file['chemin_fichier'] ?>   </td>
                                    <td><?php echo $file['date_creation']?>     </td>
                                    <td><i class="bi bi-download"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
        } else if (selected_item === 'Gestion Partenaires') {
            $(".getFiles").hide();
            $(".table-client").show(); // Afficher la table des clients
            $(".table-fichiersTraites").hide(); //Masquer la table des fichiers traités

        } else if (selected_item === 'Traitements') {
            $(".getFiles").hide();
            $(".table-client").hide(); // Afficher la table des clients
            $(".table-fichiersTraites").show(); //Masquer la table des fichiers traités
        } else {
            $(".getFiles").hide();
            $(".table-client").hide(); // Afficher la table des clients
            $(".table-fichiersTraites").hide(); //Masquer la table des fichiers traités        }
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
});

</script>
</body>
</html>