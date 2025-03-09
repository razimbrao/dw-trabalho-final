<?php

use Php\Dw\Connect;

function createCrimeTypesDimension(array $rows): void
{
    $crimeTypes = [];
    foreach($rows as $row) {
        $crimeType = trim($row["primary_type"]);

        if(array_key_exists($crimeType, $crimeTypes)) {
            continue;
        }

        $crimeTypes[$crimeType] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO crime_types (crime_type) VALUES (:crime_type)";

    foreach($crimeTypes as $crimeType => $value) {
        $pdo->prepare($sql)->execute([":crime_type" => $crimeType]);
    }
}
