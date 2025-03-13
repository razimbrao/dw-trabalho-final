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

        foreach(array_keys($crimeTypesMethod) as $key){
            if(array_key_exists($key, self::$crimeType)){
                unset($crimeTypesMethod[$key]);
            }
        }

        self::$crimeType = array_merge(self::$crimeType, $crimeTypesMethod);

        $pdo = Connect::getInstance();

        $sql = "INSERT INTO location_descriptions (description) VALUES (:description)";

        foreach ($crimeTypesMethod as $crimeType => $value) {
            try {
                $pdo->prepare($sql)->execute([':description' => $crimeType]);
            } catch (Exception $e) {
                dd($locationDescription);
            }
        }
    }

    public function getLocationDescriptions(): array
    {
        return self::$locationDescriptions;
    }
}
