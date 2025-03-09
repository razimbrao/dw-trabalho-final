<?php

use Php\Dw\Connect;

require_once __DIR__ . "/vendor/autoload.php";

$pdo = Connect::getInstance();

//$pdo->exec("DROP TABLE IF EXISTS staging_area");

$sql = "CREATE TABLE IF NOT EXISTS staging_area (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    case_number VARCHAR(50),
    date DATETIME,
    block VARCHAR(255),
    iucr INT,
    primary_type VARCHAR(100),
    description TEXT,
    location_description VARCHAR(255),
    arrest BOOLEAN,
    domestic BOOLEAN,
    beat INT,
    district INT,
    ward INT,
    community_area INT,
    fbi_code INT,
    x_coordinate DECIMAL(12,6),
    y_coordinate DECIMAL(12,6),
    year INT,
    updated_on DATETIME,
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    location VARCHAR(255)
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crime_description (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    description VARCHAR(255) UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS locale_description (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    description VARCHAR(255) UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS locals (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    latitude VARCHAR(50),
    longitude VARCHAR(50)
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crime_dates (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    crime_date DATETIME UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crime_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    crime_type VARCHAR(255) UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS iucrs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    iucr VARCHAR(50) UNIQUE
);";

$pdo->exec($sql);