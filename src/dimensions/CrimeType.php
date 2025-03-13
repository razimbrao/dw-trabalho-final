<?php

namespace Php\Dw\dimensions;

use Exception;
use Php\Dw\Connect;

class CrimeType
{
    private static ?CrimeType $instance = null;
    private static array $crimeType = [];

    private function __construct() {}

    public static function getInstance(): CrimeType
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createCrimeTypeDimension(array $rows): void
    {
        $crimeTypesMethod = [];

        foreach ($rows as $row) {
            if (is_null($row["primary_type"])) {
                continue;
            }
            $crimeType = trim($row["primary_type"]);

            if (array_key_exists($crimeType, $crimeTypesMethod)) {
                continue;
            }

            $crimeTypesMethod[$crimeType] = 1;
        }

        $pdo = Connect::getInstance();

        $sql = "INSERT INTO crime_types (crime_type) VALUES (:crime_type) ON CONFLICT (crime_type) DO NOTHING";

        foreach ($crimeTypesMethod as $crimeType => $value) {
            try {
                $pdo->prepare($sql)->execute([':crime_type' => $crimeType]);
            } catch (Exception $e) {
                dd($crimeType, $e);
            }
        }
    }
}
