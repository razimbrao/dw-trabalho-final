<?php

use Php\Dw\Connect;

function createLocaleDescriptionsDimension(array $rows): void
{
    $locals = [];
    foreach($rows as $row) {
        $latitude = trim($row["latitude"]);
        $longitude = trim($row["longitude"]);

        $location = $latitude . "#" . $longitude;

        if(array_key_exists($location, $locals)) {
            continue;
        }

        $locals[$latitude . "#" . $longitude] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO locals (latitude, longitude) VALUES (:latitude, :longitude)";

    foreach($locals as $local => $value) {
        [$latitude, $longitude] = explode("#", $local);
        $pdo->prepare($sql)->execute([":latitude" => $latitude, ":longitude" => $longitude]);
    }
}
