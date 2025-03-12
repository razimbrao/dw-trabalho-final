<?php

use Php\Dw\Connect;
use Php\Dw\Csv;
const STAGING_AREA_HEADER_TRANSLATION = [
    "ID"                   => "id",
    "Case Number"          => "case_number",
    "Date"                 => "date",
    "Block"                => "block",
    "IUCR"                 => "iucr",
    "Primary Type"         => "primary_type",
    "Description"          => "description",
    "Location Description" => "location_description",
    "Arrest"               => "arrest",
    "Domestic"             => "domestic",
    "Beat"                 => "beat",
    "District"             => "district",
    "Ward"                 => "ward",
    "Community Area"       => "community_area",
    "FBI Code"             => "fbi_code",
    "X Coordinate"         => "x_coordinate",
    "Y Coordinate"         => "y_coordinate",
    "Year"                 => "year",
    "Updated On"           => "updated_on",
    "Latitude"             => "latitude",
    "Longitude"            => "longitude",
    "Location"             => "location",
];

$pdo = Connect::getInstance();

$pdo->exec("DELETE FROM staging_area");

function createStagingArea(Csv $csv) {
    $x = 0;
    $rows = [];
    foreach ($csv->readCsv() as $row) {
        $insertRow = [];
        foreach ($csv->header as $key => $header) {
            $databaseField = STAGING_AREA_HEADER_TRANSLATION[$header];
             $insertRow[$databaseField] = $row[$key];
        }
        $rows[] = $insertRow;
        if (count($rows) === 1000) {
            insertIntoStagingArea($rows);
            $rows = [];
            $x++;
            break;
        }
    }
}

function convertTo24HourFormat(string $datetime): string
{
    $date = \DateTime::createFromFormat('m/d/Y h:i:s A', $datetime);
    if ($date === false) {
        $date = \DateTime::createFromFormat('m/d/Y H:i:s', $datetime);
    }

    return $date ? $date->format('Y-m-d H:i:s') : $datetime;
}

function insertIntoStagingArea(array $rows): void
{
    if (empty($rows)) {
        return;
    }

    $pdo = Connect::getInstance();

    $fields = [
        'id',
        'case_number',
        'date',
        'block',
        'iucr',
        'primary_type',
        'description',
        'location_description',
        'arrest',
        'domestic',
        'beat',
        'district',
        'ward',
        'community_area',
        'fbi_code',
        'x_coordinate',
        'y_coordinate',
        'year',
        'updated_on',
        'latitude',
        'longitude',
        'location'
    ];

    $placeholders = [];
    $params = [];

    foreach ($rows as $index => $row) {
        $rowPlaceholders = [];
        foreach ($fields as $field) {
            if ($field === 'date' || $field === 'updated_on') {
                $value = convertTo24HourFormat($row[$field]);
            } else {
                $value = $row[$field];
            }

            if (in_array($field, ['x_coordinate', 'y_coordinate', 'latitude', 'longitude']) && $value === '') {
                $value = NULL;
            }

            $placeholder = ':' . $field . $index;
            $rowPlaceholders[] = $placeholder;
            $params[$field . $index] = $value;
        }
        $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
    }

    $sql = "INSERT INTO staging_area (" . implode(', ', $fields) . ") VALUES " . implode(', ', $placeholders);
    //dd($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}
