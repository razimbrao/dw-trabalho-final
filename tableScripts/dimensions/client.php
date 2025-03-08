<?php

use Php\Dw\Connect;

function createClientDimension(array $rows): void
{
    $clientNames = [];
    foreach($rows as $row) {
        $clientName = ucfirst(
            trim($row["cliente"])
        );

        if(array_key_exists($clientName, $clientNames)) {
            continue;
        }

        $clientNames[$clientName] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO clients (client) VALUES (:client)";

    foreach($clientNames as $clientName => $value) {
        $pdo->prepare($sql)->execute([":client" => $clientName]);
    }
}
