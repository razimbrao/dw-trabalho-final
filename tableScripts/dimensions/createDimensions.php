<?php

use Php\Dw\Connect;

require_once __DIR__ . "/product.php";
require_once __DIR__ . "/locals.php";
require_once __DIR__ . "/crimesDescriptions.php";

function createDimensions(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM locals");
    $pdo->exec("DELETE FROM crime_dates");
    $pdo->exec("DELETE FROM crimes_descriptions");
    $pdo->exec("DELETE FROM crime_types");
    $pdo->exec("DELETE FROM iucrs");
    $pdo->exec("DELETE FROM locale_description");
    $stmt = $pdo->query("SELECT * FROM staging_area LIMIT 10");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    createLocalsDimension($rows);
    createCrimesDescriptionsDimension($rows);
    //createProductDimension($rows);
    //createClientDimension($rows);
    //createOrderDateDimension($rows);
    //createOrderDayDimension($rows);
    //createFactSales();
    //createDailySalesFact();
    //createAggSalesFact();
}