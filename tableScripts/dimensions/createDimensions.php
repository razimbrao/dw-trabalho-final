<?php

use Php\Dw\Connect;
require_once __DIR__ . "/locals.php";
require_once __DIR__ . "/crimeDescriptions.php";
require_once __DIR__ . "/crimeDates.php";
require_once __DIR__ . "/crimeTypes.php";
require_once __DIR__ . "/locationDescriptions.php";
require_once __DIR__ . "/iucrs.php";
require_once __DIR__ . "/crimeTypes.php";

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

    $totalRegisters = 100000;
    //$totalRegisters = 1000;
    $actualRegisters = 0;
    do{
        $limitRegister = $actualRegisters + 100;
        $stmt = $pdo->query("SELECT * FROM staging_area LIMIT $limitRegister OFFSET $actualRegisters");
        $actualRegisters += 100;
        var_dump($actualRegisters);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        createLocalsDimension($rows);
        createLocationDescriptionsDimension($rows);
        createCrimeDescriptionsDimension($rows);
        createCrimeTypesDimension($rows);
        createIUCRsDimension($rows);
        createCrimeDatesDimension($rows);

    } while($actualRegisters < $totalRegisters);

}