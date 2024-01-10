<?php
include "../models/client_class.php";
include "pagination.php"; // Inclure le fichier pagination.php

// Instanciez la classe Db_client
$dbClient = new Db_client();

// Obtenez tous les clients
$clients = $dbClient->getAllClients();

// Nombre d'éléments par page
$perPage = 15;

// Page actuelle (par défaut à la première page si non définie)
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Sélectionnez les clients pour la page actuelle
$start = ($currentPage - 1) * $perPage;
$end = $start + $perPage;
$selectedClients = array_slice($clients, $start, $perPage);

// Générez le HTML du tableau
$html = '<table class="table table-bordered">';
$html .= '<h2>Liste des clients</h2>';
$html .= '<thead>';
$html .= '<tr>';
$html .= '<th class="id_client">COMPTE</th>';
$html .= '<th class="nom_client"><i class="bi bi-person"></i>PARTENAIRE</th>';
$html .= '<th class="action">Action</th>';
$html .= '</tr>';
$html .= '</thead>';

$html .= '<tbody>';
foreach ($selectedClients as $client) {
    $html .= '<tr>';
    $html .= '<td>' . $client['compte'] . '</td>';
    $html .= '<td><strong>' . strtoupper($client['nomclient']) . '</strong></td>';
    $html .= "<td style='display:flex;' data-class='".$client['nomclient']."'><i class='bi bi-eye show_stat' style='' title='voir stat'><i class='bi bi-pencil-square edit_client' title='modifier partenaire'></i><i class='bi bi-save save_client' title='enregistrer modif'></i><i class='bi bi-trash3-fill delete_client' title='supprimer partenaire'></i></td>";


    $html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';

// Afficher le tableau
echo $html;

// Calculer le nombre total de pages
$totalPages = ceil(count($clients) / $perPage);

// Afficher la pagination
generatePagination($totalPages, $currentPage);
?>

<script>
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
                                $(".card-body .card-title_international").html(response[0]["ktck"]);
                                $('.card-body .card-title_montantInt').html(response[0]["mtn_tck"]);
    
                            }else if(i===1){
                                $(".card-body .card-title_national").html(response[1]["ktck"]);
                                $('.card-body .card-title_montantNat').html(response[1]["mtn_tck"]);
                            } else {
                                $(".card-body .card-title_national").html(0);
                                $(".card-body .card-title_international").html(0);
                                $('.card-body .card-title_montantNat').html(0);
                                $('.card-body .card-title_montantInt').html(0);
    
                            }
                        }
                    }else{
                        $(".card-body .card-title_national").html(response[0]["ktck"]);
                            $(".card-body .card-title_international").html(0);
                            $('.card-body .card-title_montantNat').html(response[0]["mtn_tck"]);
                            $('.card-body .card-title_montantInt').html(0);
                    }
                },
                error: function(){
                    console.log("Erreur lors d'une tentative d'affichage de la page statistique");
                }
            });
        });

        $(".table-statistiques button").on('click', function(){
            $(".table-statistiques").hide();
            $(".table-client").show();
        });
</script>
