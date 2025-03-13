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
        $sql = "INSERT INTO crime_dates (crime_date) VALUES (:crime_date) ON CONFLICT (crime_date) DO NOTHING";
        foreach ($crimeDates as $crimeDate => $value) {
            $pdo->prepare($sql)->execute([":crime_date" => $crimeDate]);
        }
    } catch (Exception $exception) {
        dd($sql, $crimeDate, $exception);
    }
}