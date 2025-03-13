<?php

namespace Php\Dw\dimensions;

use Exception;
use Php\Dw\Connect;

class LocationDescription
{
    private static ?LocationDescription $instance = null;
    private static array $locationDescriptions = [];

    private function __construct() {}

    public static function getInstance(): LocationDescription
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createLocationDescriptionsDimension(array $rows): void
    {
        self::$locationDescriptions = [];

        foreach ($rows as $row) {
            if (is_null($row["location_description"])) {
                continue;
            }
            $locationDescription = ucfirst(trim($row["location_description"]));

            if (array_key_exists($locationDescription, self::$locationDescriptions)) {
                continue;
            }

            self::$locationDescriptions[$locationDescription] = 1;
        }

        $pdo = Connect::getInstance();

        $sql = "INSERT INTO location_descriptions (description) VALUES (:description)";

        foreach (self::$locationDescriptions as $locationDescription => $value) {
            try {
                $pdo->prepare($sql)->execute([':description' => $locationDescription]);
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
