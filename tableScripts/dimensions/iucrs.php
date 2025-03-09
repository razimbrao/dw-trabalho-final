<?php

use Php\Dw\Connect;

function createIUCRsDimension(array $rows): void
{
    $IUCRs = [];
    foreach($rows as $row) {
        $IUCR = trim($row["iucr"]);

        if(array_key_exists($IUCR, $IUCRs)) {
            continue;
        }

        $IUCRs[$IUCR] = 1;
    }

    $pdo = Connect::getInstance();

    $sql = "INSERT INTO iucrs (iucr) VALUES (:iucr)";

    foreach($IUCRs as $IUCR => $value) {
        $pdo->prepare($sql)->execute([":iucr" => $IUCR]);
    }
}
