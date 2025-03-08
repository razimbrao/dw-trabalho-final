<?php

use Php\Dw\Connect;

function createProductDimension(array $rows): void
{
    $productNames = [];
    foreach($rows as $row) {
        $productName = trim($row["produto"]);
        if(array_key_exists($productName, $productNames)) {
            continue;
        }
        $productNames[$productName] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO products (product) VALUES (:product)";

    foreach($productNames as $productName => $value) {
        $pdo->prepare($sql)->execute([":product" => $productName]);
    }
}
