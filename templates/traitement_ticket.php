<?php

require_once "connexion.php";

$HOST = "localhost";
$PORT = "5432";
$DBNAME = "sms_pro_database";
$PWD = "Wizzle21#";

try {
    $dsn = "pgsql:host=$HOST;port=$PORT;dbname=$DBNAME;user=moranta;password=$PWD";
    $db = new PDO($dsn);

    $sql = "SELECT compte, destination FROM billing WHERE compte LIKE 'MBK%' OR compte LIKE 'WTS%'";
    $queryCompte = $db->prepare($sql);
    $queryCompte->execute();
    $resultCompte = $queryCompte->fetchAll(PDO::FETCH_ASSOC);

    $result_json = json_encode($resultCompte);
    echo $result_json;

    #dest SBGS
    $search = array('MBK00001');
    foreach($result_json as $compte){
        if(in_array($search, $compte['compte']) && $compte['destination']==='Les autres operateurs'){
            $compte['CODE'] = '_off';
        }elseif(in_array($search, $compte['compte']) && $compte['destination']==='ORANGE'){
            $compte['CODE'] = '_on';
        }elseif(in_array($search, $compte['compte']) && compte['destination']==='International'){
            $compte['compte'] = '_int';
        }
    }
    // $query

} catch (Exception $e) {
    echo $e -> getMessage();
}
?>