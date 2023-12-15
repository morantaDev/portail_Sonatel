<?php

require '../vendor/autoload.php';
require_once "connexion.php";
require_once "../helpers/database_class.php";


try {
    $db = new DatabaseConnection();
    $db = $db->getConnection();

    $sql = "SELECT compte, destination, nombre_sms_mois as trafic FROM billing WHERE compte LIKE 'MBK%' OR compte LIKE 'WTS%'";
    $queryCompte = $db->prepare($sql);
    $queryCompte->execute();
    $resultCompte = $queryCompte->fetchAll(PDO::FETCH_ASSOC);

    // Dest SBGS
    $search = array('MBK00001');
    $finalTable = [];

    foreach ($resultCompte as $compte) {
        if (in_array($compte['compte'], $search) && $compte['destination'] === 'Les autres operateurs') {
            $compte['CODE'] = '_off';
        } elseif (in_array($compte['compte'], $search) && $compte['destination'] === 'ORANGE') {
            $compte['CODE'] = '_on';
        } elseif (in_array($compte['compte'], $search) && $compte['destination'] === 'International') {
            $compte['CODE'] = '_int';
        } else {
            $compte['CODE'] = null;
        }

        // Dest autres comptes
        $destinations = array('ORANGE', 'Les autres operateurs');
        if (empty($compte['CODE']) && in_array($compte['destination'], $destinations)) {
            $compte['CODE'] = '_nat';
        } elseif (empty($compte['CODE']) && $compte['destination'] === 'International') {
            $compte['CODE'] = '_int';
        }

        // Création colonne ID
        $compte['ID'] = $compte['compte'] . $compte['CODE'];

        // Calcul de la somme du trafic total pour chaque ID
        $traficTotal[$compte['ID']] = isset($traficTotal[$compte['ID']]) ? $traficTotal[$compte['ID']] + $compte['trafic'] : $compte['trafic'];

        // Ajouter l'entrée modifiée au tableau final
        $compte['trafic_total'] = $traficTotal[$compte['ID']];

        // 
        $compte['TYPE'] = substr($compte['compte'], 0, 3);
        
        // Ajouter l'entrée modifiée au tableau final
        $finalTable[] = $compte;   
    }

    //Supprimer les doublons
    $finalTable = array_map("unserialize", array_unique(array_map("serialize", $finalTable)));

    // echo count($finalTable);

    
    
    $sqlCat = "SELECT * FROM catalogue";
    $queryCatalogue = $db->prepare($sqlCat);
    $queryCatalogue->execute();
    $resultCatalogue = $queryCatalogue->fetchAll(PDO::FETCH_ASSOC);

    
    // Ingestion de catalogue
    foreach ($finalTable as &$row) {
        foreach ($resultCatalogue as $catalogueRow) {
            if ($row['TYPE'] === $catalogueRow['type'] && $row['CODE'] === $catalogueRow['code']) {
                $row = array_merge($row, $catalogueRow);
                break;
            }
        }
    }

    
    
    // Calcul du montant du ticket
    foreach ($finalTable as &$row) {
        $row['MTN_TCK'] = $row['tarif'] * $row['trafic_total'];
        
        // Création de nouvelles colonnes
        $row['NTICKET'] = 453;
        $row['CPROD'] = 22;
        $row['TYPE_TCK'] = 1;
        $row['SENS'] = 1;
    }
    
    
    
    // Réarrangement colonnes
    $finalTable = array_map(function ($row) {
        return [
            'Compte' => $row['compte'],
            'NTICKET' => $row['NTICKET'],
            'CPROD' => $row['CPROD'],
            'TYPE_TCK' => $row['TYPE_TCK'],
            'SENS' => $row['SENS'],
            'MTN_TCK' => $row['MTN_TCK'],
            'trafic_total' => $row['trafic_total'],
            'TARIF' => $row['tarif'],
            'KTCK' => $row['ktck'],
        ];
    }, $finalTable);
    
    
    // var_dump($finalTable);
    
    
    
    //Filtres CBAO, WAVE & UBA

    $cbaoSvn = array_filter($finalTable, function ($row) {
        return $row['Compte'] === 'MBK00002' && $row['KTCK'] === 'sms vers national';
    });
    
    $cbaoSvi = array_filter($finalTable, function ($row) {
        return $row['Compte'] === 'MBK00002' && $row['KTCK'] === "sms vers l'international";
    });
    
    $ubaSvn = array_filter($finalTable, function ($row) {
        return $row['Compte'] === 'MBK00510' && $row['KTCK'] === 'sms vers national';
    });
    
    $ubaSvi = array_filter($finalTable, function ($row) {
        return $row['Compte'] === 'MBK00510' && $row['KTCK'] === "sms vers l'international";
    });
    
    $finalTable = array_filter($finalTable, function ($row) {
        return $row['Compte'] !== 'MBK00002' && $row['Compte'] !== 'MBK00510';
    });
    
    // Calcul MTN_TCK pour CBAO contrat Octobre 2022
    
    foreach ($cbaoSvn as &$row) {
        $row['MTN_TCK'] = $row['trafic_total'] * 5;
    }
    
    foreach ($cbaoSvi as &$row) {
        $row['MTN_TCK'] = $row['TARIF'] * $row['trafic_total'];
    }
    
    // Calcul MTN_TCK pour UBA
    foreach ($ubaSvn as &$row) {
        $row['MTN_TCK'] = $row['trafic_total'] * 5;
    }
    
    foreach ($ubaSvi as &$row) {
        $row['MTN_TCK'] = $row['TARIF'] * $row['trafic_total'];
    }
    
    // Append finalTable
    $finalTable = array_merge($finalTable, $cbaoSvn, $cbaoSvi, $ubaSvn, $ubaSvi);
    
    foreach ($finalTable as &$row) {
        $row['trafic_total'] = strval($row['trafic_total']);
        $row['KTCK'] = $row['trafic_total'] . ' ' . $row['KTCK'];
        $row['MTN_TCK'] = (int)$row['MTN_TCK'];

        //Récupérer la date du ticket
        $sql = "SELECT mois_fac as date FROM  billing LIMIT 1;";
        $query = $db->prepare($sql);
        $query->execute();
        $resultDate = $query->fetch(PDO::FETCH_ASSOC);
        // print_r($resultDate['date']);

        $row['DATOP_TCK'] = $resultDate['date'];
    }
    
    // print_r($finalTable);

    //Récupérer le catalogue pour les engamgements
    $queryOSM = $db->prepare("SELECT * FROM osm");
    $queryOSM->execute();
    $resultOSM = $queryOSM->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($resultOSM);

    // Définir une fonction personnalisée pour appliquer les transformations
    function applyTransformations($row) {
        global $finalTable;

        $compte = $row['compte'];
        $tarif = $row['trafic_associe'];
        $montantEngagement = $row['montant'];

        $conditions = array_filter($finalTable, function ($finalTableRow) use ($compte, $tarif) {
            return $finalTableRow['Compte'] === $compte && strpos($finalTableRow['KTCK'], 'vers national') !== false;
        });


        foreach ($conditions as &$condition) {
            if ($condition['trafic_associe'] <= $tarif) {
                $condition['MTN_TCK'] = 0;
                $condition['KTCK'] = $tarif . ' sms vers national';
            } elseif ($condition['trafic_associe'] > $tarif) {
                $condition['MTN_TCK'] = (5 * $condition['trafic_associe']) - $montantEngagement;
            }

            $condition['CA'] = 5 * $condition['trafic_associe'];
            // Pour ceux qui n'ont pas d'engagement
            $condition['MTN_TCK'] = $tarif * $condition['trafic_associe'];
        }

        return $row;  // Retournez la ligne mise à jour
    }

    // Appliquez la fonction à chaque ligne de la DataFrame 'engagements'
    $engagements = array_map('applyTransformations', $resultOSM);


    // Bon CA
    foreach ($finalTable as &$row) {
        if (!isset($row['CA'])) {
            $row['CA'] = $row['MTN_TCK'];
        }
    }

    // Calcul infos wave
    $traficVersOrange = 14736427;
    $montantOn = 2 * $traficVersOrange;
    
    $ktckOn = ' ' . $traficVersOrange . '  sms vers Orange';
    
    $traficVersAutresOp = 2109;
    
    $montantOff = 3 * $traficVersAutresOp;
    
    $ktckOff = $traficVersAutresOp . ' sms vers les autres opérateurs';
    
    $finalTable = array_map(function ($row) {
        return [
            'Compte' => $row['Compte'],
            'NTICKET' => $row['NTICKET'],
            'CPROD' => $row['CPROD'],
            'TYPE_TCK' => $row['TYPE_TCK'],
            'DATOP_TCK' => $row['DATOP_TCK'],
            'SENS' => $row['SENS'],
            'MTN_TCK' => $row['MTN_TCK'],
            'KTCK' => $row['KTCK'],
        ];
    }, $finalTable);
    
    
    
    $query = $db->prepare("SELECT mois_fac FROM billing LIMIT 1");
    $query->execute();
    $lastDate=$query->fetchColumn();
    
    
    // Convertir le tableau final en JSON
    // $resultJson = json_encode($finalTable);
    // echo $resultJson;
    
    // Export fichier
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $worksheet = $spreadsheet->getActiveSheet();
    

    // var_dump($finalTable);

    // Ajoutez les en-têtes
    $headers = array_keys($finalTable[0]);
    foreach ($headers as $colIndex => $header) {
        echo $colIndex;
        $worksheet->setCellValueByColumnAndRow($colIndex + 1, 1, $header);
    }

// Ajoutez les données
foreach ($finalTable as $rowIndex => $row) {
    for ($i = 0; $i < count($row); $i++) {
        $worksheet->setCellValueByColumnAndRow($i + 1, $rowIndex + 2, $row[array_keys($row)[$i]]);
    }
}



$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('Tickets_SMS_PLUS_Bis_'.$lastDate.'.xlsx');



    $data = \PhpOffice\PhpSpreadsheet\IOFactory::load('Tickets_SMS_PLUS_Bis_202309.xlsx');



} catch (Exception $e) {
    echo $e->getMessage();
}
?>
