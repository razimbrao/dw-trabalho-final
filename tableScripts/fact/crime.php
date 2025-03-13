<?php

use Php\Dw\Connect;
function createCrimeFact(): void
{
    $pdo = Connect::getInstance();
    $pdo->exec("DELETE FROM crimes");

    $sql = "INSERT INTO crimes (arrest, crime_description_id, location_description_id, local_id, crime_date_id, crime_type_id, iucr_id) VALUES (:arrest, :crime_description_id, :location_description_id, :local_id, :crime_date_id, :crime_type_id, :iucr_id)";
    $stmtInsert = $pdo->prepare($sql);

    $actualRegisters = 0;
    $totalRegisters = 10000;

    //$totalRegisters = 8271000;
   
    do{
        $limitRegister = $actualRegisters + 100;
        $stmt = $pdo->query("SELECT * FROM staging_area LIMIT $limitRegister OFFSET $actualRegisters");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $actualRegisters += 100;
        var_dump($actualRegisters);

        foreach($rows as $key => $row) {

            $insert = [];
            $insert[":arrest"] = filter_var($row["arrest"], FILTER_VALIDATE_BOOLEAN);
            $insert[":crime_description_id"] = getCrimeDescriptionId($row["description"]);
            if(!is_null($row["location_description"])) {
                $insert[":location_description_id"] = getLocationDescriptionId($row["location_description"]);
            } else {
                unset($insert[":local_id"]);
            }

            if (!is_null($row["latitude"]) && !is_null($row["longitude"])) {
                $insert[":local_id"] = getLocalId($row["latitude"], $row["longitude"]);
            } else {
                unset($insert[":local_id"]);
            }
            if ($insert[":local_id"] === 0) {
                dd($row);
            }
            $insert[":crime_date_id"] = getCrimeDateId($row["date"]);
            $insert[":crime_type_id"] = getCrimeTypeId($row["primary_type"]);
            $insert[":iucr_id"] = getIucrId($row["iucr"]);

            $stmtInsert->bindValue(':arrest', $insert[":arrest"], PDO::PARAM_BOOL);
            $stmtInsert->bindValue(':crime_description_id', $insert[":crime_description_id"], PDO::PARAM_INT);
            if(is_null($insert[":location_description_id"])) {
                $stmtInsert->bindValue(':location_description_id', null, PDO::PARAM_NULL);
            } else {
                $stmtInsert->bindValue(':location_description_id', $insert[":location_description_id"], PDO::PARAM_INT);
            }
            if (is_null($insert[":local_id"])) {
                $stmtInsert->bindValue(':local_id', null, PDO::PARAM_NULL);
            } else {
                $stmtInsert->bindValue(':local_id', $insert[":local_id"], PDO::PARAM_INT);
            }
            $stmtInsert->bindValue(':crime_date_id', $insert[":crime_date_id"], PDO::PARAM_INT);
            $stmtInsert->bindValue(':crime_type_id', $insert[":crime_type_id"], PDO::PARAM_INT);
            $stmtInsert->bindValue(':iucr_id', $insert[":iucr_id"], PDO::PARAM_INT);
            try {
                $stmtInsert->execute();
            } catch (PDOException $e) {
                echo ($e->getMessage());
                //dd($insert);
            }
        }
    }while($actualRegisters < $totalRegisters);
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
    $stmt->execute([":crime_date" => date("Y-m-d H:i:s", strtotime($date))]);
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