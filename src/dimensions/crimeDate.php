<?php

namespace Php\Dw\dimensions;

use Exception;
use Php\Dw\Connect;

class CrimeDate
{
    private static ?CrimeDate $instance = null;
    private static array $crimeDates = [];

    private function __construct() {}

    public static function getInstance(): CrimeDate
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createCrimeDateDimension(array $rows): void
    {
        $crimeDatesMethod = [];

        foreach ($rows as $row) {
            $crimeDate = date("Y-m-d H:i:s", strtotime($row["date"]));
            $updateDate = date("Y-m-d H:i:s", strtotime($row["updated_on"]));

            if (!array_key_exists($crimeDate, self::$crimeDates)) {
                $crimeDatesMethod[$crimeDate] = 1;
            }
            if (!array_key_exists($updateDate, self::$crimeDates)) {
                $crimeDatesMethod[$updateDate] = 1;
            }
        }

        $pdo = Connect::getInstance();

        try {
            $sql = "INSERT INTO crime_dates (crime_date) VALUES (:crime_date) ON CONFLICT (crime_date) DO NOTHING";
            $stmt = $pdo->prepare($sql);
            foreach (self::$crimeDates as $crimeDate => $value) {
                $stmt->execute([':crime_date' => $crimeDate]);
            }
        } catch (Exception $exception) {
            dd($sql, $crimeDate, $exception);
        }
    }

    public function getCrimeDates(): array
    {
        return self::$crimeDates;
    }
}


// function createCrimeDaysDimension(array $rows): void
// {
//     $crimeDates = [];
//     foreach ($rows as $row) {
//         $fullDate = trim($row["date"]);
//         $fullUpdate = trim($row["updated_on"]);

//         // Assuming you want only the day part; adjust as necessary
//         $simpleDate = explode('/', $fullDate)[0];
//         $simpleUpdate = explode('/', $fullUpdate)[0];

//         if (!array_key_exists($simpleDate, $crimeDates)) {
//             $crimeDates[$simpleDate] = 1;
//         }
//         if (!array_key_exists($simpleUpdate, $crimeDates)) {
//             $crimeDates[$simpleUpdate] = 1;
//         }
//     }

//     $pdo = Connect::getInstance();

//     $sql = "INSERT INTO crime_days (crime_day) VALUES (:crime_day)";
//     $stmt = $pdo->prepare($sql);

//     foreach ($crimeDates as $crimeDay => $_) {
//         try {
//             $stmt->execute([":crime_day" => $crimeDay]);
//         } catch (Exception $e) {
//             // Optionally log error details here
//             continue;
//         }
//     }
// }
