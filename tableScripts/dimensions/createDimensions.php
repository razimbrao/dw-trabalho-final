<?php

use Php\Dw\Connect;
use Php\Dw\dimensions\CrimeDate;
use Php\Dw\dimensions\CrimeDay;
use Php\Dw\dimensions\CrimeDescription;
use Php\Dw\dimensions\CrimeType;
use Php\Dw\dimensions\Iucr;
use Php\Dw\dimensions\Locals;
use Php\Dw\dimensions\LocationDescription;

function createDimensions(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM locals");
    $pdo->exec("DELETE FROM crime_dates");
    $pdo->exec("DELETE FROM crime_descriptions");
    $pdo->exec("DELETE FROM crime_types");
    $pdo->exec("DELETE FROM iucrs");
    $pdo->exec("DELETE FROM location_descriptions");
    $pdo->exec("DELETE FROM crime_dates");
    $pdo->exec("DELETE FROM crime_days");
    $pdo->exec("DELETE FROM crimes");

    // $totalRegisters = 8269600;
    $totalRegisters = 1000;
    $actualRegisters = 0;
    do{
        $limitRegister = $actualRegisters + 100;
        $stmt = $pdo->query("SELECT * FROM staging_area LIMIT $limitRegister OFFSET $actualRegisters");
        $actualRegisters += 100;
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        LocationDescription::getInstance()->createLocationDescriptionsDimension($rows);
        CrimeType::getInstance()->createCrimeTypeDimension($rows);
        Iucr::getInstance()->createIucrDimension($rows);
        Locals::getInstance()->createLocalDimension($rows);
        CrimeDate::getInstance()->createCrimeDateDimension($rows);
        CrimeDay::getInstance()->createCrimeDayDimension($rows);
        CrimeDescription::getInstance()->createCrimeDescriptionDimension($rows);

    } while($actualRegisters < $totalRegisters);

}