<?php

use Php\Dw\Connect;

require_once __DIR__ . "/locals.php";
require_once __DIR__ . "/crimesDescriptions.php";
require_once __DIR__ . "/crimeTypes.php";
require_once __DIR__ . "/locationDescriptions.php";
require_once __DIR__ . "/crimeTypes.php";

function createDimensions(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM locals");
    $pdo->exec("DELETE FROM crime_dates");
    $pdo->exec("DELETE FROM crime_descriptions");
    $pdo->exec("DELETE FROM crime_types");
    $pdo->exec("DELETE FROM iucrs");
    $pdo->exec("DELETE FROM location_descriptions");
    $stmt = $pdo->query("SELECT * FROM staging_area LIMIT 10");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    createLocalsDimension($rows);
    createLocationDescriptionsDimension($rows);
    createCrimeDescriptionsDimension($rows);
    createCrimeTypesDimension($rows);
    //createProductDimension($rows);
    //createClientDimension($rows);
    //createOrderDateDimension($rows);
    //createOrderDayDimension($rows);
    //createFactSales();
    //createDailySalesFact();
    //createAggSalesFact();
}