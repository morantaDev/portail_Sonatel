<?php 
    include 'flash.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sélection de Fichier</title>
    <style>

        .saly {
            background-color: black; /* Couleur de fond de la div */
            padding: 20px; /* Espace intérieur de la div */
            margin: 50px auto; /* Centrer la div */
            border-radius: 10px; /* Coins arrondis de la div */
            width: 80%; /* Largeur de la div */
            max-width: 400px; /* Largeur maximale de la div */
            text-align: center; /* Centrer le contenu horizontalement */
        }

        input[type="file"] {
            display: none; /* Masquer l'input par défaut */
        }

        label {
            background-color: rgb(255,140,0); /* Couleur de fond du label (forme) */
            padding: 10px; /* Espace intérieur du label (forme) */
            border-radius: 5px; /* Coins arrondis du label (forme) */
            color: black; /* Couleur du texte du label (forme) */
            cursor: pointer; /* Curseur pointer pour indiquer qu'il est cliquable */
        }

        #detailsFichier {
            margin-top: 20px; /* Espace au-dessus de la div des détails du fichier */
        }
    </style>
</head>
<body>
    <form action="chargement.php" method="post" enctype="multipart/form-data">
        <div class="saly">
            <label for="inputFichier">Sélectionnez un fichier</label>
            <input type="file" id="inputFichier" name="inputFichier[]" multiple onchange="afficherDetailsFichier()" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" />
            <div id="detailsFichier"></div>
            <input type="submit" value="Envoyer" />
        </div>
    </form>
    
    <!-- Afficher ici le message d'erreur -->
    <div class="displayFlashMessage"><?php flash('existing_billing') ?></div>
    <script>
        function afficherDetailsFichier() {
            console.log("Fonction afficherDetailsFichier appelée.");  // Ajoutez cette ligne
            var inputFichier = document.getElementById('inputFichier');
            var detailsFichier = document.getElementById('detailsFichier');

            detailsFichier.innerHTML = ""; // Réinitialiser le contenu

            // Vérifier si des fichiers ont été sélectionnés
            if (inputFichier.files && inputFichier.files.length > 0) {
                for (const fichier of inputFichier.files){
                    // var fichier = inputFichier.files[i];
    
                    // Afficher des détails sur le fichier
                    detailsFichier.innerHTML += `
                    <p>Nom du fichier: ${fichier.name}</p>
                    <p>Type du fichier: ${fichier.type}</p>
                    <p>Taille du fichier: ${fichier.size} octets</p>
                    <p>Dernière modification: ${fichier.lastModifiedDate.toLocaleDateString()}</p>
                    <hr>
                    `;
                }
            } else {
                // Aucun fichier sélectionné
                detailsFichier.innerHTML = 'Aucun fichier sélectionné.';
            }
        }
    
        // Ajoutez une fonction pour soumettre le formulaire
        // function soumettreFormulaire() {
        //     document.forms[0].submit(); // Soumettre le premier formulaire trouvé sur la page
        // }

    </script>

</body>
</html>
