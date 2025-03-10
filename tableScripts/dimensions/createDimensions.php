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
    $stmt = $pdo->query("SELECT * FROM staging_area LIMIT 10000");
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    createLocalsDimension($rows);
    createLocationDescriptionsDimension($rows);
    createCrimeDescriptionsDimension($rows);
    createCrimeTypesDimension($rows);
    createIUCRsDimension($rows);
    createCrimeDatesDimension($rows);
    createCrimeDaysDimension($rows);
}