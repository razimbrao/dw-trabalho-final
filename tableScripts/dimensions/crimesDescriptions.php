<?php

use Php\Dw\Connect;

function createCrimesDescriptionsDimension(array $rows): void
{
    $crimesDescriptions = [];
    foreach($rows as $row) {
        $description = trim($row["description"]);

        if(array_key_exists($description, $crimesDescriptions)) {
            continue;
        }

        $crimesDescriptions[$description] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO crimes_descriptions (description) VALUES (:description)";

    foreach($crimesDescriptions as $crimeDescription => $value) {
        $pdo->prepare($sql)->execute([":description" => $crimeDescription]);
    }
}
