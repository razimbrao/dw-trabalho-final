<?php

use Php\Dw\Connect;
function createCrimeFact(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM crimes");
    $stmt = $pdo->query("SELECT * from staging_area LIMIT 10000");
    $totalRegisters = 8269600;
    $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    foreach($rows as $key => $row) {
        if($key > 10000) {
            break;
        }
        $sql = "INSERT INTO crimes (arrest, crime_description_id, location_description_id, local_id, crime_date_id, crime_type_id, iucr_id) VALUES (:arrest, :crime_description_id, :location_description_id, :local_id, :crime_date_id, :crime_type_id, :iucr_id)";
        try {
            foreach($rows as $row) {
                $insert = [];
                $insert[":arrest"] = $row["arrest"];
                $insert[":crime_description_id"] = getCrimeDescriptionId($row["description"]);
                $insert[":location_description_id"] = getLocationDescriptionId($row["location_description"]);
                $insert[":local_id"] = getLocalId($row["latitude"], $row["longitude"]);
                $insert[":crime_date_id"] = getCrimeDateId($row["date"]);
                $insert[":crime_type_id"] = getCrimeTypeId($row["primary_type"]);
                $insert[":iucr_id"] = getIucrId($row["iucr"]);

                $pdo->prepare($sql)->execute($insert);
            }
        } catch (Exception $exception) {
            dd($insert, $exception);
        }
    }

}

function getCrimeDescriptionId(string $description): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM crime_descriptions WHERE description = :description");
    $stmt->execute([":description" => ucfirst($description)]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getLocationDescriptionId(string $locationDescription): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM location_descriptions WHERE description = :description");
    $stmt->execute([":description" => ucfirst($locationDescription)]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getLocalId(string $latitude, string $longitude): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM locals WHERE latitude = :latitude AND longitude = :longitude");
    $stmt->execute([":latitude" => $latitude, ":longitude" => $longitude]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getCrimeDateId(string $date): int
{

    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM crime_dates WHERE crime_date = :crime_date");
    $stmt->execute([":crime_date" => date("d/m/Y", strtotime($date))]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getCrimeTypeId(string $type): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM crime_types WHERE crime_type = :crime_type");
    $stmt->execute([":crime_type" => $type]);
    $id = $stmt->fetchColumn();
    return $id;
}

function getIucrId(string $iucr): int
{
    $pdo = Connect::getInstance();
    $stmt = $pdo->prepare("SELECT id FROM iucrs WHERE iucr = :iucr");
    $stmt->execute([":iucr" => $iucr]);
    $id = $stmt->fetchColumn();
    return $id;
}