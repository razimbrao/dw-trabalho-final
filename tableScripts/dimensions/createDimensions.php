<?php

use Php\Dw\Connect;

require_once __DIR__ . "/product.php";
require_once __DIR__ . "/client.php";
require_once __DIR__ . "/order.php";

function createDimensions(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM locals");
    $pdo->exec("DELETE FROM crime_dates");
    $pdo->exec("DELETE FROM crime_types");
    $stmt = $pdo->query("SELECT * FROM staging_area LIMIT 10");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    createProductDimension($rows);
    createClientDimension($rows);
    createOrderDateDimension($rows);
    createOrderDayDimension($rows);
    createFactSales();
    createDailySalesFact();
    createAggSalesFact();
}