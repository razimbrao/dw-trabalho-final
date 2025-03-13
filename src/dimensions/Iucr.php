<?php

namespace Php\Dw\dimensions;

use Exception;
use Php\Dw\Connect;

class Iucr
{
    private static ?Iucr $instance = null;
    private static array $iucr = [];

    private function __construct() {}

    public static function getInstance(): Iucr
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createIucrDimension(array $rows): void
    {
        $iucrsMethod = [];

        foreach ($rows as $row) {
            if (is_null($row["iucr"])) {
                continue;
            }
            $iucrType = trim($row["iucr"]);

            if (array_key_exists($iucrType, $iucrsMethod)) {
                continue;
            }

            $iucrsMethod[$iucrType] = 1;
        }

        $pdo = Connect::getInstance();

        $sql = "INSERT INTO iucrs (iucr) VALUES (:iucr) ON CONFLICT (iucr) DO NOTHING";

        foreach ($iucrsMethod as $iucrType => $value) {
            try {
                $pdo->prepare($sql)->execute([':iucr' => $iucrType]);
            } catch (Exception $e) {
                dd($iucrsMethod, self::$iucr, $e);
            }
        }
    }

    public function getLocationDescriptions(): array
    {
        return self::$iucr;
    }
}
