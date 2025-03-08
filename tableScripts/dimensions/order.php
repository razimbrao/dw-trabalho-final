<?php

use Php\Dw\Connect;


function createOrderDateDimension(array $rows): void
{
    $orderDates = [];
    foreach ($rows as $row) {
        $orderDate = $row["data_pedido"];
        if (array_key_exists($orderDate, $orderDates)) {
            continue;
        }
        $orderDates[$row["data_pedido"]] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO order_dates (order_date) VALUES (:order_date)";

    foreach ($orderDates as $orderDate => $value) {
        $pdo->prepare($sql)->execute([":order_date" => $orderDate]);
    }
}


function createOrderDayDimension(array $rows): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM order_days");

    $orderDates = [];
    foreach ($rows as $row) {
        $dateTimeStr = $row["data_pedido"];
        $releaseDateStr = $row["data_liberacao"];
        $releaseDate = DateTime::createFromFormat("d/m/Y H:i", $releaseDateStr);
        $dateTime = DateTime::createFromFormat("d/m/Y H:i", $dateTimeStr);
        if (!$dateTime || !$releaseDate) {
            continue;
        }
        $releaseDate = $releaseDate->format("d/m/Y");
        $orderDate = $dateTime->format("d/m/Y");
        if (!in_array($orderDate, $orderDates, true)) {
            $orderDates[] = $orderDate;
        }
        
        if (!in_array($releaseDate, $orderDates, true)) {
            $orderDates[] = $releaseDate;
        }
    }
    $sql = "INSERT INTO order_days (order_days) VALUES (:order_days)";

    $stmt = $pdo->prepare($sql);

    foreach ($orderDates as $orderDate) {
        try {
            $stmt->execute([
                ":order_days" => $orderDate
            ]);
        } catch (Exception $e) {
            continue;
        }
    }
}
