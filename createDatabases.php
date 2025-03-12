<?php

use Php\Dw\Connect;

require_once __DIR__ . "/vendor/autoload.php";

$pdo = Connect::getInstance();

$pdo->exec("DROP TABLE IF EXISTS staging_area");

$sql = "CREATE TABLE IF NOT EXISTS staging_area (
    id SERIAL PRIMARY KEY,
    case_number VARCHAR(50),
    date TIMESTAMP,
    block VARCHAR(255),
    iucr VARCHAR(50),
    primary_type VARCHAR(100),
    description TEXT,
    location_description VARCHAR(255),
    arrest BOOLEAN,
    domestic BOOLEAN,
    beat INT,
    district INT,
    ward INT,
    community_area INT,
    fbi_code VARCHAR(50),
    x_coordinate VARCHAR(50) NULL,
    y_coordinate VARCHAR(50) NULL,
    year INT,
    updated_on TIMESTAMP,
    latitude VARCHAR(50) NULL,
    longitude VARCHAR(50) NULL,
    location VARCHAR(255)
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crime_descriptions (
    id SERIAL PRIMARY KEY,
    description VARCHAR(255) UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS location_descriptions (
    id SERIAL PRIMARY KEY,
    description VARCHAR(255) UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS locals (
    id SERIAL PRIMARY KEY,
    latitude VARCHAR(50),
    longitude VARCHAR(50)
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crime_dates (
    id SERIAL PRIMARY KEY,
    crime_date TIMESTAMP UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crime_days (
    id SERIAL PRIMARY KEY,
    crime_day TIMESTAMP UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crime_types (
    id SERIAL PRIMARY KEY,
    crime_type VARCHAR(255) UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS iucrs (
    id SERIAL PRIMARY KEY,
    iucr VARCHAR(50) UNIQUE
);";

$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS crimes (
    id SERIAL PRIMARY KEY,
    arrest BOOLEAN,
    crime_description_id INTEGER,
    location_description_id INTEGER,
    local_id INTEGER NULL,
    crime_date_id INTEGER,
    crime_type_id INTEGER,
    iucr_id INTEGER,
    FOREIGN KEY(crime_description_id) REFERENCES crime_descriptions(id) ON DELETE CASCADE,
    FOREIGN KEY(location_description_id) REFERENCES location_descriptions(id) ON DELETE CASCADE,
    FOREIGN KEY(local_id) REFERENCES locals(id) ON DELETE CASCADE,
    FOREIGN KEY(crime_date_id) REFERENCES crime_dates(id) ON DELETE CASCADE,
    FOREIGN KEY(crime_type_id) REFERENCES crime_types(id) ON DELETE CASCADE,
    FOREIGN KEY(iucr_id) REFERENCES iucrs(id) ON DELETE CASCADE
);";

$pdo->exec($sql);
