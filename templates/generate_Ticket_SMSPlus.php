<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    require '../vendor/autoload.php';
    require_once "../helpers/database_class.php";
    
    session_start();
    include 'flash.php';

    
try {
    $db = new DatabaseConnection();
    $db = $db->getConnection();

    if (!empty($_POST) && $_SERVER["REQUEST_METHOD"] === 'POST') {
        $id_fichier = (int) trim($_POST["idFichier"]);
        echo $id_fichier;

        $sql = "SELECT compte as ND,NTICKET,CPROD,TYPE_TCK,DATOP_TCK,SENS,MTN_TCK,KTCK FROM donnees_tickets WHERE id_fichier=?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_fichier]);
        $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // var_dump($datas);

        $query = $db->prepare("SELECT mois_fac FROM billing LIMIT 1");
        $query->execute();
        $lastDate = $query->fetchColumn();
        // echo $lastDate;

        $split_date = explode("-", $lastDate);
    
        $new_date = $split_date[0] . '' . $split_date[1];
        // Export fichier
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Ajoutez les en-têtes
        $headers = array_keys($datas[0]);
        foreach ($headers as $colIndex => $header) {
            $worksheet->setCellValueByColumnAndRow($colIndex + 1, 1, strtoupper($header));
        }

        // Ajoutez les données
        foreach ($datas as $rowIndex => $row) {
            for ($i = 0; $i < count($row); $i++) {
                $worksheet->setCellValueByColumnAndRow($i + 1, $rowIndex + 2, $row[array_keys($row)[$i]]);
            }
        }

        $fileName = 'Tickets_SMS_PLUS_Bis_' . $new_date . '.xlsx';
        $filePath = '/home/moranta/Downloads/output/' . $fileName;


        //confirm  download message
        // echo "Vous êtes sur le point de técharger le fichier. Cliquer sur ok pour continuer.";
        // Save the Excel file to the server
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);


        // Enregistrez le téléchargement dans l'historique
        $action = "Téléchargement du fichier " . $fileName;
        $sql = "INSERT INTO historique (action) VALUES (?)";
        $stmt = $db->prepare($sql);
        if ($stmt->execute([$action])) {
            // Répondre avec le lien de téléchargement
            echo json_encode(["success" => true, "message" => "Le fichier a été téléchargé avec succès.", "download_link" => $filePath]);

            $getAllHistoriques_sql = "SELECT action, date_creation FROM historique";
            $stmt_historique = $db->prepare($getAllHistoriques_sql);
            
            // Exécutez la requête
            $stmt_historique->execute();
            
            // Récupérez les résultats
            $allHistorique = $stmt_historique->fetchAll(PDO::FETCH_ASSOC);
            

            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');

            // Répondre avec les historiques au format JSON
            header('Content-Type: application/json');
            echo json_encode($allHistorique);

            flash('success_download', 'téléchargement fait avec succès', 'green');
            
        } else {
            // Afficher les erreurs
            echo json_encode(["success" => false, "message" => "Erreur lors de l'insertion dans l'historique : " . implode(", ", $stmt->errorInfo())]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID du fichier non fourni"]);
    }
    

} catch (Exception $e) {
    echo $e->getMessage();
}


?>
