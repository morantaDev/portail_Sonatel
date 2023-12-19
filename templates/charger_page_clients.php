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
$html .= '<th class="id_client">Id Partenaire</th>';
$html .= '<th class="nom_client"><i class="bi bi-person"></i>PARTENAIRE</th>';
$html .= '<th class="action">Action</th>';
$html .= '</tr>';
$html .= '</thead>';

$html .= '<tbody>';
foreach ($selectedClients as $client) {
    $html .= '<tr>';
    $html .= '<td>' . $client['id_client'] . '</td>';
    $html .= '<td><strong>' . strtoupper($client['nomclient']) . '</strong></td>';
    $html .= '<td style="display:flex;"><i class="bi bi-pencil-square"></i><i class="bi bi-trash3-fill"></i></td>';

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
