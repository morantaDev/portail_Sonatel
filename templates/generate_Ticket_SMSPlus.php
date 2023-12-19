<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require '../vendor/autoload.php';
    require_once "../helpers/database_class.php";

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
        echo $lastDate;
        // Export fichier
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        // Ajoutez les en-têtes
        $headers = array_keys($datas[0]);
        foreach ($headers as $colIndex => $header) {
            $worksheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
        }

        // Ajoutez les données
        foreach ($datas as $rowIndex => $row) {
            for ($i = 0; $i < count($row); $i++) {
                $worksheet->setCellValueByColumnAndRow($i + 1, $rowIndex + 2, $row[array_keys($row)[$i]]);
            }
        }

        $fileName = 'test' . $lastDate . '.xlsx';
        $filePath = '/home/moranta/Downloads/output/' . $fileName;

        // Save the Excel file to the server
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
    
    }else {
        echo "ID du fichier non fourni";
    }

} catch (Exception $e) {
    echo $e->getMessage();
}


?>
