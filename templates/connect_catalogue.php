<?php


$HOST = "localhost";
$PORT = "5432";
$DBNAME = "sms_pro_database";
$PWD = "Wizzle21#";

try {
    $dsn = "pgsql:host=$HOST;port=$PORT;dbname=$DBNAME;user=moranta;password=$PWD";
    $db = new PDO($dsn);

    $sqlCat = "SELECT * FROM catalogue";
    $queryCatalogue = $db->prepare($sqlCat);
    $queryCatalogue->execute();
    $resultCatalogue = $queryCatalogue->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($resultCatalogue as $row) {
        // Iterate through each key-value pair in the $row array
        foreach ($row as $key => $value) {
            echo "$key: $value<br>";
        }
        echo "<br>"; // Add a line break for better readability
    }

}catch(Exception $e){
    echo $e ->getMessage();
}
?>