<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

session_start();

if(!$_SESSION['User_Id']){
    die("L'id de utilisateur n'est pas bien stocké dans les sessions");
    exit();
} else {
    $User_Id = $_SESSION['User_Id'];
    $User_Id = $User_Id['id_utilisateur'];
}



$HOST = "localhost";
$PORT = "5432";
$DBNAME = "sms_pro_database";
$PWD = "Wizzle21#";

try {
    $dsn = "pgsql:host=$HOST;port=$PORT;dbname=$DBNAME;user=moranta;password=$PWD";
    $db = new PDO($dsn);


    //Récupérer le chemin du fichier
    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        if (isset($_FILES['inputFichier']) && is_array($_FILES['inputFichier']['name'])) {
            echo "".$User_Id["id_utilisateur"];
            $myFiles = count($_FILES['inputFichier']['name']);
    
    
            for ($i = 0; $i < $myFiles; $i++) {
                $nomFichier = $_FILES['inputFichier']['name'][$i];
                $typeFichier = $_FILES['inputFichier']['type'][$i];
                $tailleFichier = $_FILES['inputFichier']['size'][$i];
                $emplacementTemporaire = $_FILES['inputFichier']['tmp_name'][$i];
    
            
                // Spécifiez l'emplacement où vous souhaitez déplacer le fichier
                $emplacementDestination = "/home/moranta/Downloads/fichiersTest/" . strtolower($nomFichier);
    
                // Déplacez le fichier téléchargé vers l'emplacement de destination
                if (move_uploaded_file($emplacementTemporaire, $emplacementDestination)) {
                    echo "Nom du fichier: $nomFichier\n";
                    echo "Type du fichier: $typeFichier\n";
                    echo "Emplacement du fichier temporaire: $emplacementTemporaire\n";
                    echo "Emplacement du fichier de destination: $emplacementDestination\n";
                    echo "Taille du fichier: $tailleFichier octets\n";
                    echo "l'id de l'utilisateur est:" .$User_Id;
                    echo "Le fichier a été déplacé avec succès.";
                    // Reste du code...
                } else {
                    echo "Erreur lors du déplacement du fichier.";
                    echo "Erreur lors du déplacement du fichier.\n";
                    echo "Erreur PHP: " . $_FILES['inputFichier']['error'][$i] . "\n";
                    switch ($_FILES['inputFichier']['error'][$i]) {
                        case UPLOAD_ERR_INI_SIZE:
                            echo "La taille du fichier téléchargé dépasse la valeur upload_max_filesize du fichier ini.\n";
                            break;
                        case UPLOAD_ERR_FORM_SIZE:
                            echo "La taille du fichier téléchargé dépasse la valeur MAX_FILE_SIZE spécifiée dans le formulaire HTML.\n";
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            echo "Le fichier n'a été que partiellement téléchargé.\n";
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            echo "Aucun fichier n'a été téléchargé.\n";
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            echo "Le dossier temporaire est manquant.\n";
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            echo "Échec de l'écriture du fichier sur le disque.\n";
                            break;
                        case UPLOAD_ERR_EXTENSION:
                            echo "Une extension PHP a arrêté l'envoi du fichier.\n";
                            break;
                        default:
                            echo "Erreur inconnue.\n";
                            break;
                    }
                }
                if(str_contains($emplacementDestination, "billing.")){
                    echo "\r\nle chemin du fichier billing est:" .$emplacementDestination;
                    // Charger le fichier Excel
                    // $excelFilePath = '/home/moranta/Downloads/billing.202310.xlsx';
                    $excelFilePath = $emplacementDestination;
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFilePath);
                
                    // Obtenez le mois à partir du nom du fichier
                    // $excelFilePath = '/home/moranta/Downloads/billing.202310.xlsx';
                
                    if (isset($excelFilePath) && !empty($excelFilePath)) {
                        $monthInFileName = pathinfo($excelFilePath, PATHINFO_FILENAME);  // Obtenir le nom du fichier sans extension
                        $monthInFileName = preg_replace('/[^0-9]/', '', $monthInFileName);  // Supprimer tout sauf les chiffres
                        $monthInFileName = substr_replace($monthInFileName, '01', 6, 0);  // Ajouter '01' au bon endroit
                        
                        // Reformatez la chaîne de mois pour qu'elle corresponde à "YYYY-MM"
                        $formattedMonthObject = DateTime::createFromFormat('Ymd', $monthInFileName);
                        
                        if ($formattedMonthObject === false) {
                            die("Erreur lors du formatage de la date.");
                        }
                        
                        // Obtenez la date formatée sous forme de chaîne
                        $formattedMonth = $formattedMonthObject->format('Y-m-d');
                        echo "Mois de facturation : $formattedMonth\n";
                    } else {
                        die("Le chemin du fichier est incorrect ou non défini.");
                    }
                
                    // Sélectionner la feuille de calcul active
                    $sheet = $spreadsheet->getActiveSheet();

                    //Secletionner les clients de façon unique
                    // $uniqueName = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
                    // $uniqueName->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::Conditional::CONDITION_UNIQUE);    

                    // $UniqueConditionalApply -> $spreadsheet -> getActiveSheet();
                    
                
                    $insertQuery = $db->prepare("INSERT INTO billing (client, compte, nombre_sms_mois, libelle, destination, mois_fac, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    

                    // Préparez la requête d'insertion pour les clients
                    $insertClient = $db->prepare("INSERT INTO client (nomclient) VALUES (?)");

                    // Préparez la requête de vérification si le client existe déjà
                    $clientExists = $db->prepare("SELECT COUNT(*) AS count FROM client WHERE nomclient = ?");



                    // Parcourir les lignes de la feuille de calcul et insérer dans la table "billing"
                    foreach ($sheet->getRowIterator() as $index => $row) {
                        if ($index === 1) {
                            continue;
                        }
                        $rowData = $sheet->rangeToArray('A' . $row->getRowIndex() . ':' . $sheet->getHighestColumn() . $row->getRowIndex(), null, true, false);
                        // var_dump($rowData[0][2]);
                        // var_dump($rowData);
                
                         // Exécuter la requête avec les valeurs liées
                         $insertQuery->execute([$rowData[0][0], $rowData[0][1], $rowData[0][2], $rowData[0][4], $rowData[0][3], $formattedMonth, $User_Id]);
                        //  $insertClient ->execute([$rowData[0][0]]);

    
                        // Exécuter la requête pour vérifier si le client existe déjà
                        $clientExists->execute([$rowData[0][0]]);
                        $clientExistsCount = $clientExists->fetchColumn();

                        if ($clientExistsCount > 0) {
                            // Le nom du client existe déjà
                            echo "Le nom du client existe déjà.";
                        } else {
                            // Le nom du client n'existe pas encore, donc l'insérer
                            $insertClient->execute([$rowData[0][0]]);
                        }        
                    }
                        // Exécuter la requête pour vérifier si le client existe déjà
                        $clientExists->execute([$rowData[0][0]]);
                        $clientExistsCount = $clientExists->fetchColumn();

                        if ($clientExistsCount > 0) {
                            // Le nom du client existe déjà
                            echo "Le nom du client existe déjà.";
                        } else {
                            // Le nom du client n'existe pas encore, donc l'insérer
                            $insertClient->execute([$rowData[0][0]]);
                        }
                              
                    echo "Importation réussie.";

                } elseif (str_contains($emplacementDestination, "catalogue")) {
                    $excelFilePath = $emplacementDestination;

                    


                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFilePath);

                    // $highestRow = $worksheet->getHighestDataRow();

                    $worksheet = $spreadsheet->getSheetByName("Feuil1");
                    $highestColumn = $worksheet->getHighestDataColumn();
                    $insertCatalogue = $db->prepare("INSERT INTO catalogue (type, code, tarif, ktck) VALUES (?, ?, ?, ?)");
                    $ExistingCode = $db->prepare("SELECT COUNT(*) AS count FROM catalogue WHERE code = ?");

                    foreach ($worksheet->getRowIterator() as $index => $row){
                        if ($index === 1) {
                            continue;
                        }
                        $rowData = $worksheet->rangeToArray('A' . $row->getRowIndex() . ':' . $highestColumn . $row->getRowIndex(), null, true, false);

                        var_dump($rowData); 


                        $insertCatalogue->execute([$rowData[0][0], $rowData[0][1],$rowData[0][2], $rowData[0][3]]);
                        // $ExistingCode->execute([$rowData[0][1]]);
                        // $ExistingCodeCount=$ExistingCode->fetchColumn();

                        // if($ExistingCodeCount > 0){
                        //     echo "Le code existe déjà dans la base de données";
                        // } else {
                        //     $insertCode->execute($rowData[0][0]);
                        // }
                    }
                    
                    $worksheet1 = $spreadsheet->getSheetByName("engagements");
                    $highestColumn1 = $worksheet1->getHighestDataColumn();
                    $insertEngagement = $db->prepare("INSERT INTO osm (compte, trafic_associe, montant) VALUES (?, ?, ?)");
                    // $UpdateCatalogue= $db->prepare("UPDATE offre_sur_mesure SET indicateur_osm=?;");
                    
                    foreach ($worksheet1->getRowIterator() as $index => $row){
                        if ($index === 1) {
                                continue;
                            }
                        $rowData = $worksheet1->rangeToArray('A' . $row->getRowIndex() . ':' . $highestColumn1 . $row->getRowIndex(), null, true, false);
                        var_dump($rowData[0][0]);   //Récupération des colonnes
                        
                        $prix_osm = ($rowData[0][2]) ? $rowData[0][2] : 0;
                        // if($prix_osm>0){
                        //     // $UpdateCatalogue
                        //     $UpdateCatalogue->execute(['true']);
                        // }
                        // var_dump($prix_osm);
                        
                        //inserer les osm dans la table offre_sur_mesure
                        $insertEngagement->execute([$rowData[0][0], $rowData[0][1], $prix_osm]);
                        
                    }




                    // for ($row = 1; $row <= $highestRow; ++$row) {
                    //     for ($col = 'A'; $col <= $highestColumn; ++$col) {
                    //         $cellValue = $worksheet->getCell($col . $row)->getValue();
                    //         var_dump($cellValue);
                    //     }
                    // }
                        // echo '<table>' . "\n";
                        // for ($row = 1; $row <= $highestRow; ++$row) {
                        //     echo '<tr>' . PHP_EOL;
                        //     for ($col = 'A'; $col <= $highestColumn; ++$col) {
                        //         echo '<td>' .
                        //             $worksheet->getCell($col . $row)
                        //                 ->getValue() .
                        //             '</td>' . PHP_EOL;
                        //     }
                        //     echo '</tr>' . PHP_EOL;
                        // }
                        // echo '</table>' . PHP_EOL;
                    
                    echo "Importation réussie.";
                }
            }
        }
    } else {
        echo "Erreur lors du téléchargement du fichier.";
    }


} catch (Exception $e) {
    echo $e->getMessage();
}

?>