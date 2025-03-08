<?php

use Php\Dw\Connect;

require_once __DIR__ . "/product.php";
require_once __DIR__ . "/client.php";
require_once __DIR__ . "/order.php";

function createDimensions(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM products");
    $pdo->exec("DELETE FROM clients");
    $pdo->exec("DELETE FROM order_dates");
    $stmt = $pdo->query("SELECT * FROM staging_area");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    createProductDimension($rows);
    createClientDimension($rows);
    createOrderDateDimension($rows);
    createOrderDayDimension($rows);
    createFactSales();
    createDailySalesFact();
    createAggSalesFact();
}