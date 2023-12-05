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
                $nomFichier = strtolower($_FILES['inputFichier']['name'][$i]);
                $typeFichier = $_FILES['inputFichier']['type'][$i];
                $tailleFichier = $_FILES['inputFichier']['size'][$i];
                $emplacementTemporaire = $_FILES['inputFichier']['tmp_name'][$i];
    
            
                // Spécifiez l'emplacement où vous souhaitez déplacer le fichier
                $emplacementDestination = "/home/moranta/Downloads/fichiersTest/" . $nomFichier;
    
                // Déplacez le fichier téléchargé vers l'emplacement de destination
                if (move_uploaded_file($emplacementTemporaire, $emplacementDestination)) {
                    echo "Nom du fichier: $nomFichier\n";
                    echo "Type du fichier: $typeFichier\n";
                    echo "Emplacement du fichier temporaire: $emplacementTemporaire\n";
                    echo "Emplacement du fichier de destination: $emplacementDestination\n";
                    echo "Taille du fichier: $tailleFichier octets\n";
                    echo "l'id de l'utilisateur est:" .$User_Id;
                } else {
                    echo "Erreur lors du déplacement du fichier.";
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
                
                    $insertQuery = $db->prepare("INSERT INTO billing (nombre_sms_mois, libelle, destination, mois_fac, id_utilisateur) VALUES (?, ?, ?, ?, ?)");
                    $insertClient = $db->prepare("INSERT INTO client (nomclient) VALUES (?)");

                    // Parcourir les lignes de la feuille de calcul et insérer dans la table "billing"
                    foreach ($sheet->getRowIterator() as $index => $row) {
                        if ($index === 1) {
                            continue;
                        }
                        $rowData = $sheet->rangeToArray('A' . $row->getRowIndex() . ':' . $sheet->getHighestColumn() . $row->getRowIndex(), null, true, false);
                        // var_dump($rowData[0][2]);
                        // var_dump($rowData);
                
                         // Exécuter la requête avec les valeurs liées
                         $insertQuery->execute([$rowData[0][2], $rowData[0][4], $rowData[0][3], $formattedMonth, $User_Id]);
                         $insertClient ->execute([$rowData[0][0]]);
                        }
                
                    echo "Importation réussie.";

                } elseif (str_contains($emplacementDestination, "catalogue")) {
                    $excelFilePath = $emplacementDestination;
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                    $reader ->setReadDataOnly(TRUE);
                    $spreadsheet -> $reader -> load($excelFilePath);

                    $worksheet -> $spreadsheet ->getActiveSheet();

                    foreach ($worksheet -> getRowIterator() as $row){
                        echo $row;
                    }
                    
                }
            }
        } else {
            echo "Erreur lors du téléchargement du fichier.";
        }
    }


} catch (Exception $e) {
    echo $e->getMessage();
}

?>
