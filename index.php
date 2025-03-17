<?php
error_reporting(E_ERROR | E_PARSE);
use Php\Dw\Connect;
use Php\Dw\Csv;

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/createDatabases.php";
require_once __DIR__ . "/tableScripts/stagingAreaConfig.php";
require_once __DIR__ . "/tableScripts/dimensions/createDimensions.php";

$csv = new Csv(__DIR__ . "/Crimes_-_2001_to_Present.csv");
createStagingArea($csv);
try {
    createDimensions();
} catch (\PDOException $e) {
    dd($e);
}
