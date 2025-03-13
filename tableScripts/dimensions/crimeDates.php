<?php

use Php\Dw\Connect;

function createCrimeDatesDimension(array $rows): void
{
    $crimeDates = [];
    foreach ($rows as $row) {
        // Convert to ISO 8601 format: "Y-m-d H:i:s"
        $crimeDate = date("Y-m-d H:i:s", strtotime($row["date"]));
        $updateDate = date("Y-m-d H:i:s", strtotime($row["updated_on"]));

        // Ensure unique date values are stored
        if (!array_key_exists($crimeDate, $crimeDates)) {
            $crimeDates[$crimeDate] = 1;
        }
        if (!array_key_exists($updateDate, $crimeDates)) {
            $crimeDates[$updateDate] = 1;
        }
    }

    $pdo = Connect::getInstance();

    try {
        $sql = "INSERT INTO crime_dates (crime_date) VALUES (:crime_date)";
        foreach ($crimeDates as $crimeDate => $value) {
            $pdo->prepare($sql)->execute([":crime_date" => $crimeDate]);
        }
    } catch (Exception $exception) {
        dd($sql, $crimeDate, $exception);
    }
}


function createCrimeDaysDimension(array $rows): void
{
    $crimeDates = [];
    foreach ($rows as $row) {
        $fullDate = trim($row["date"]);
        $fullUpdate = trim($row["updated_on"]);

        // Assuming you want only the day part; adjust as necessary
        $simpleDate = explode('/', $fullDate)[0];
        $simpleUpdate = explode('/', $fullUpdate)[0];

        if (!array_key_exists($simpleDate, $crimeDates)) {
            $crimeDates[$simpleDate] = 1;
        }
        if (!array_key_exists($simpleUpdate, $crimeDates)) {
            $crimeDates[$simpleUpdate] = 1;
        }
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO crime_days (crime_day) VALUES (:crime_day)";
    $stmt = $pdo->prepare($sql);

    foreach ($crimeDates as $crimeDay => $_) {
        try {
            $stmt->execute([":crime_day" => $crimeDay]);
        } catch (Exception $e) {
            // Optionally log error details here
            continue;
        }
    }
}
