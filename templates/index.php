<?php
    session_start();
    include 'flash.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Inclure le fichier CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css"> -->
    <title>Login page</title>

    <style>
    body {
        width: 100%;
        height: 100%;
        margin: 0; /* Pour enlever les marges par défaut du body */
        padding: 0;
    }
    .container {
        height: 100vh; /* 100% de la hauteur de la vue */
        position: relative; /* Position relative pour positionner les éléments enfants */
        padding: 0; /* Supprimer le padding pour utiliser toute la hauteur */
        border: 1px solid black;
        max-width: 100%;
    }
    .sonatel1_logo {
        background: url('../assets/SONATEL.jpg') center/cover no-repeat; /* Utiliser l'image comme fond */
        height: 100%; /* Utiliser toute la hauteur du conteneur */
        width: 100%;
        background-size: cover;
    }
    .login_page {
        position: absolute; /* Position absolue par rapport au conteneur parent */
        top: 50%; /* Positionner au centre verticalement */
        left: 50%; /* Positionner au centre horizontalement */
        transform: translate(-50%, -50%); /* Ajuster pour centrer parfaitement */
        padding: 20px; /* Ajout de padding pour l'espace intérieur */
        background-color: rgba(255, 255, 255, 0.9); /* Fond semi-transparent pour le formulaire */
        border-radius: 10px; /* Ajouter des coins arrondis */
        width: 22%;
        height: 40%;
        box-shadow: rgba(0, 0, 0, 0.07) 0px 1px 2px, rgba(0, 0, 0, 0.07) 0px 2px 4px, rgba(0, 0, 0, 0.07) 0px 4px 8px, rgba(0, 0, 0, 0.07) 0px 8px 16px, rgba(0, 0, 0, 0.07) 0px 16px 32px, rgba(0, 0, 0, 0.07) 0px 32px 64px;    }
    .login_page h3{
        padding: 10px 20px;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        font-weight: bold;
        margin-bottom: 30px;
    }
    .login_page button{
        width: 100%;
        max-width: 100%;
        margin: 15px 0;
        position: relative;
        top: -5px;
    }
    .login_page input{
        border: none;
        background-color: transparent;
        
    }
    .Connexion{
        padding-top: 30px;
    }
    /* Ajoutez ces styles dans votre balise style après vos styles existants */

    /* Pour les écrans de petite taille (téléphones) */
    @media (max-width: 767px) {
        .login_page {
            width: 80%; /* Ajustez la largeur pour les petits écrans */
        }
    }

    /* Pour les écrans de taille moyenne (tablettes) */
    @media (min-width: 768px) and (max-width: 991px) {
        .login_page {
            width: 60%; /* Ajustez la largeur pour les tablettes */
        }
    }

    /* Pour les écrans de grande taille (ordinateurs de bureau) */
    @media (min-width: 22%) {
        .login_page {
            width: 40%; /* Ajustez la largeur pour les ordinateurs de bureau */
        }
    }
    .connexion-btn {
        max-height: 50%; /* Limiter la hauteur du bouton à 100% du conteneur parent */
    }

    </style>
</head>
<body>
    <div class="container">
        <div class="sonatel1_logo"></div>
        <form action="handle_database.php" method="post">
            <div class="login_page">
                <h3>Login</h3>
                <div class="form-group" style="display: flex; color: black; border-bottom: 2px solid grey; padding-top: 10px;">
                    <!-- <label for="UserName">Nom d’utilisateur</label> -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16" style="margin: 2px;">
                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"/>
                      </svg>
                    <input type="text" class="form-control" id="UserName" name="UserName" placeholder="Entrer le nom d'utilisateur ou Email" autocomplete="username" required>
                </div>
                <div class="form-group" style="display: flex; color: black; border-bottom: 2px solid grey; padding-top: 10px;">
                    <!-- <label for="MotDePasse">Mot de passe</label> -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16" style="margin: 2px;">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                      </svg>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Entrer le mot de passe" autocomplete="current-password" required>
                </div>
                <div class="Connexion">
                    <button type="submit" class="btn btn-primary btn-lg connexion-btn">Connexion
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16" style="position: relative; top: 10px;">
                                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                            </svg>
                        </span>
                    </button>
                </div>
                <?php echo flash('error_login')?>
            </div>
        </form>
    </div>
    <!-- Inclure le fichier JavaScript de Bootstrap si nécessaire -->
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> -->
</body>
</html>
<script>
    // Utilisez jQuery pour masquer le message après un délai
    $(document).ready(function() {
        setTimeout(function() {
            $('#flash-message').fadeOut('slow');
        }, 2000); 
    });
</script>