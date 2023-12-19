<?php

require_once "../helpers/database_class.php";


try {
    $db = new DatabaseConnection();
    $db = $db->getConnection();
    
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