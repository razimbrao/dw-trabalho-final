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

//$pdo->exec("DELETE FROM staging_area");

function createStagingArea(Csv $csv) {
    $rows = [];
    try {
        foreach ($csv->readCsv() as $row) {
            $insertRow = [];
            foreach ($csv->header as $key => $header) {
                try {
                    $databaseField = STAGING_AREA_HEADER_TRANSLATION[$header];
                    $insertRow[$databaseField] = $row[$key];
                } catch (exception $e) {
                    //dd($databaseField);
                }
            }
            $rows[] = $insertRow;
            if (count($rows) === 1000) {
                insertIntoStagingArea($rows);
                $rows = [];
                break;
            }
        }
    } catch (exception $e) {
        echo $e->getMessage();
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
            if ($field === 'date' || $field === 'updated_on' && !is_null($row[$field])) {
                $value = convertTo24HourFormat($row[$field]);
            } else {
                $value = $row[$field];
            }

            if (in_array($field, ['x_coordinate', 'y_coordinate', 'latitude', 'longitude']) && $value === '') {
                $value = null;
            }

            if (!isset($value) || $value === '' || strtolower($value) === 'null') {
                $value = null;
            }

            $placeholder = ':' . $field . $index;
            $rowPlaceholders[] = $placeholder;
            $params[$field . $index] = $value;
        }
        $placeholders[] = '(' . implode(', ', $rowPlaceholders) . ')';
    }

    try {
        $sql = "INSERT INTO staging_area (" . implode(', ', $fields) . ") VALUES " . implode(', ', $placeholders);
    } catch (Exception $e) {
        echo $e->getMessage();
        //dd($sql);
    }

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $paramType = PDO::PARAM_STR;
        if (is_null($value)) {
            $paramType = PDO::PARAM_NULL;
        } elseif (is_bool($value)) {
            $paramType = PDO::PARAM_BOOL;
        } elseif (is_int($value)) {
            $paramType = PDO::PARAM_INT;
        }
        $stmt->bindValue(':' . $key, $value, $paramType);
    }
    $stmt->execute();
}