<?php

namespace Php\Dw\dimensions;

use Exception;
use Php\Dw\Connect;

class CrimeDescription
{
    private static ?CrimeDescription $instance = null;
    private static array $crimeDescriptions = [];

    private function __construct() {}

    public static function getInstance(): CrimeDescription
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createCrimeDescriptionDimension(array $rows): void
    {
        $crimeDescriptionsMethod = [];

        foreach ($rows as $row) {
            $description = ucfirst(trim($row["description"]));

            if (array_key_exists($description, self::$crimeDescriptions)) {
                continue;
            }

            $crimeDescriptionsMethod[$description] = 1;
        }

        foreach(array_keys($crimeDescriptionsMethod) as $key){
            if(array_key_exists($key, self::$crimeDescriptions)){
                unset($crimeDescriptionsMethod[$key]);
            }
        }
       
        self::$crimeDescriptions = array_merge(self::$crimeDescriptions, $crimeDescriptionsMethod);


        $pdo = Connect::getInstance();

        $sql = "INSERT INTO crime_descriptions (description) VALUES (:description)";

        foreach (self::$crimeDescriptions as $crimeDescription => $value) {
            $pdo->prepare($sql)->execute([':description' => $crimeDescription]);
        }
    }

    public function getCrimeDescriptions(): array
    {
        return self::$crimeDescriptions;
    }
}
