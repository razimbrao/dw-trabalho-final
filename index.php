<?php

use Php\Dw\Connect;
use Php\Dw\Csv;

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/createDatabases.php";
require_once __DIR__ . "/tableScripts/stagingAreaConfig.php";
require_once __DIR__ . "/tableScripts/dimensions/createDimensions.php";
require_once __DIR__ . "/tableScripts/fact/crime.php";

//$csv = new Csv(__DIR__ . "/Crimes_-_2001_to_Present.csv");

//createStagingArea($csv);
createDimensions();
createCrimeFact();
//createDailySalesFact();
