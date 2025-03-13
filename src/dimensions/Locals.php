<?php

namespace Php\Dw\dimensions;

use Exception;
use Php\Dw\Connect;

class Locals
{
    private static ?Locals $instance = null;
    private static array $locals = [];

    private function __construct() {}

    public static function getInstance(): Locals
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createLocalDimension(array $rows): void
    {
        $localMethod = [];

        foreach ($rows as $row) {
            if(is_null($row["latitude"]) || is_null($row["longitude"])) {
                continue;
            }

            $latitude = trim($row["latitude"]);
            $longitude = trim($row["longitude"]);

            $locationType = $latitude . "#" . $longitude;

            if (array_key_exists($locationType, $localMethod)) {
                continue;
            }

            $localMethod[$locationType] = 1;
        }

        foreach(array_keys($localMethod) as $key){
            if(array_key_exists($key, self::$locals)){
                unset($localMethod[$key]);
            }
        }

        self::$locals = array_merge(self::$locals, $localMethod);

        $pdo = Connect::getInstance();

        $sql = "INSERT INTO locals (latitude, longitude) VALUES (:latitude, :longitude)";

        foreach ($localMethod as $localType => $value) {
            try {
                [$latitude, $longitude] = explode("#", $localType);
                $pdo->prepare($sql)->execute([":latitude" => $latitude, ":longitude" => $longitude]);
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
