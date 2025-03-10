<?php

use Php\Dw\Connect;

function createLocationDescriptionsDimension(array $rows): void
{
    $locationDescriptions = [];
    foreach($rows as $row) {
        $locationDescription = ucfirst(trim($row["location_description"]));

        if(array_key_exists($locationDescription, $locationDescriptions)) {
            continue;
        }

        $locationDescriptions[$locationDescription] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO location_descriptions (description) VALUES (:description)";

    foreach($locationDescriptions as $locationDescription => $value) {
        $pdo->prepare($sql)->execute([":description" => $locationDescription]);
    }
}
