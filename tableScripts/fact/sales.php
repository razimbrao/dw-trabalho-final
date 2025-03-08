<?php

use Php\Dw\Connect;
function createFactSales(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM sales");
    $stmt = $pdo->query("SELECT * from staging_area");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $sql = "INSERT INTO sales (product_id, client_id, order_date_id, total_amount) VALUES (:product_id, :client_id, :order_date_id, :total_amount)";
    foreach($rows as $row) {
        $insert = [];
        $insert[":product_id"] = getProductId($row["produto"]);
        $insert[":client_id"] = getClientId($row["cliente"]);
        $insert[":order_date_id"] = getOrderDateId($row["data_pedido"]);
        $insert[":total_amount"] = $row["valor_total"];
        $pdo->prepare($sql)->execute($insert);
    }
}

function createDailySalesFact(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM daily_sales");

    $stmt = $pdo->query("SELECT * from staging_area");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $product_id = getProductId($row["produto"]);
        $client_id = getClientId($row["cliente"]);
        $dateTime = DateTime::createFromFormat("d/m/Y H:i", $row["data_pedido"]);
        $orderDate = $dateTime->format("d/m/Y");
        $order_date_id = getOrderDayId($orderDate);

        $checkStmt = $pdo->prepare("
            SELECT total_amount FROM daily_sales 
            WHERE product_id = :product_id 
            AND client_id = :client_id 
            AND order_date_id = :order_date_id
        ");
        $checkStmt->execute([
            ":product_id"   => $product_id,
            ":client_id"    => $client_id,
            ":order_date_id" => $order_date_id
        ]);
        $existingSale = $checkStmt->fetch(\PDO::FETCH_ASSOC);

        if ($existingSale) {
            $totalAmount = $existingSale["total_amount"] + $row["valor_total"];
            $updateStmt = $pdo->prepare("
                UPDATE daily_sales 
                SET total_amount = :total_amount
                WHERE product_id = :product_id 
                AND client_id = :client_id 
                AND order_date_id = :order_date_id
            ");
            $updateStmt->execute([
                ":product_id"    => $product_id,
                ":client_id"     => $client_id,
                ":order_date_id" => $order_date_id,
                ":total_amount"  => $totalAmount
            ]);
        } else {
            $insertStmt = $pdo->prepare("
                INSERT INTO daily_sales (product_id, client_id, order_date_id, total_amount) 
                VALUES (:product_id, :client_id, :order_date_id, :total_amount)
            ");
            $insertStmt->execute([
                ":product_id"   => $product_id,
                ":client_id"    => $client_id,
                ":order_date_id" => $order_date_id,
                ":total_amount"  => $row["valor_total"]
            ]);
        }
    }
}

function createAggSalesFact(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM agr_sales");

    $stmt = $pdo->query("SELECT * from staging_area");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $product_id = getProductId($row["produto"]);
        $client_id = getClientId($row["cliente"]);
        $orderTime = DateTime::createFromFormat("d/m/Y H:i", $row["data_pedido"]);
        $releaseTime = DateTime::createFromFormat("d/m/Y H:i", $row["data_liberacao"]);
        $releaseDate = '';
        if ($releaseTime) {
            $releaseDate = $releaseTime->format("d/m/Y");
        }
        $orderDate = $orderTime->format("d/m/Y");
        $order_date_id = getOrderDayId($orderDate);
        $release_date_id = getOrderDayId($releaseDate);

        $checkStmt = $pdo->prepare("
            SELECT total_amount FROM agr_sales 
            WHERE product_id = :product_id 
            AND client_id = :client_id 
            AND order_date_id = :order_date_id
        ");
        $checkStmt->execute([
            ":product_id"   => $product_id,
            ":client_id"    => $client_id,
            ":order_date_id" => $order_date_id
        ]);
        $existingSale = $checkStmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($existingSale) {
            $totalAmount = $existingSale["total_amount"] + $row["valor_total"];
            if ($release_date_id) {
                $updateStmt = $pdo->prepare("
                UPDATE agr_sales 
                SET total_amount = :total_amount, 
                    release_date_id = :release_date_id
                WHERE product_id = :product_id 
                AND client_id = :client_id 
                AND order_date_id = :order_date_id
                ");
                $updateStmt->execute([
                    ":product_id"    => $product_id,
                    ":client_id"     => $client_id,
                    ":order_date_id" => $order_date_id,
                    ":release_date_id" => $release_date_id,
                    ":total_amount"  => $totalAmount
                ]);
            } else {
                $updateStmt = $pdo->prepare("
                UPDATE agr_sales 
                SET total_amount = :total_amount
                WHERE product_id = :product_id 
                AND client_id = :client_id 
                AND order_date_id = :order_date_id
            ");
            $updateStmt->execute([
                ":product_id"    => $product_id,
                ":client_id"     => $client_id,
                ":order_date_id" => $order_date_id,
                ":total_amount"  => $totalAmount
            ]);
            }
        } else {
            $insertStmt = $pdo->prepare("
                INSERT INTO agr_sales (product_id, client_id, order_date_id, release_date_id, total_amount) 
                VALUES (:product_id, :client_id, :order_date_id, :release_date_id, :total_amount)
            ");
            $insertStmt->execute([
                ":product_id"   => $product_id,
                ":client_id"    => $client_id,
                ":order_date_id" => $order_date_id,
                ":release_date_id" => $release_date_id,
                ":total_amount"  => $row["valor_total"]
            ]);
        }
    }
}

function getProductId(string $productName): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM products WHERE product = :product");
    $stmt->execute([":product" => $productName]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getClientId(string $clientName): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE client = :client");
    $stmt->execute([":client" => $clientName]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getOrderDateId(string $orderDate): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM order_dates WHERE order_date = :order_date");
    $stmt->execute([":order_date" => $orderDate]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getOrderDayId(string $orderDay): int | null
{
    if (!$orderDay) {
        return null;
    }
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM order_days WHERE order_days = :order_days");
    $stmt->execute([":order_days" => $orderDay]);
    $id = $stmt->fetchColumn();
    return $id;
}