<?php

namespace Php\Dw\dimensions;

use Exception;
use Php\Dw\Connect;

class CrimeDay
{
    private static ?CrimeDay $instance = null;
    private static array $crimeDays = [];

    private function __construct() {}

    public static function getInstance(): CrimeDay
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createCrimeDayDimension(array $rows): void
    {
        $crimeDaysMethod = [];

        foreach ($rows as $row) {
            $fullDate = trim($row["date"]);
            $fullUpdate = trim($row["updated_on"]);

            $simpleDate = explode('/', $fullDate)[0];
            $simpleUpdate = explode('/', $fullUpdate)[0];

            if (!array_key_exists($simpleDate, self::$crimeDays)) {
                $crimeDaysMethod[$simpleDate] = 1;
            }
            if (!array_key_exists($simpleUpdate, self::$crimeDays)) {
                $crimeDaysMethod[$simpleUpdate] = 1;
            }
        }

        foreach(array_keys($crimeDaysMethod) as $key){
            if(array_key_exists($key, self::$crimeDays)){
                unset($crimeDaysMethod[$key]);
            }
        }
       
        self::$crimeDays = array_merge(self::$crimeDays, $crimeDaysMethod);

        $pdo = Connect::getInstance();

        $sql = "INSERT INTO crime_days (crime_day) VALUES (:crime_day)";
        $stmt = $pdo->prepare($sql);

        foreach (self::$crimeDays as $crimeDay => $_) {
            try {
                $stmt->execute([':crime_day' => $crimeDay]);
            } catch (Exception $e) {
                continue;
            }
        }
    }

    public function getCrimeDays(): array
    {
        return self::$crimeDays;
    }
}
