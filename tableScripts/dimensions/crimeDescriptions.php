<?php

use Php\Dw\Connect;

function createCrimeDescriptionsDimension(array $rows): void
{
    $crimeDescriptions = [];
    foreach($rows as $row) {
        $description = ucfirst(trim($row["description"]));

        if(array_key_exists($description, $crimeDescriptions)) {
            continue;
        }

        $crimeDescriptions[$description] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO crime_descriptions (description) VALUES (:description)  ON CONFLICT (description) DO NOTHING";

    foreach($crimeDescriptions as $crimeDescription => $value) {
        $pdo->prepare($sql)->execute([":description" => $crimeDescription]);
    }
}
