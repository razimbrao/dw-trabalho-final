<?php

namespace Php\Dw;

class Csv
{
    private(set) array $header = [];

    public function __construct(
        private string $csv
    ) {}

    public function readCsv(): iterable
    {
        $csv = fopen($this->csv, "r");
        try {
            while (($row = fgetcsv($csv, separator: ";", escape: "\\")) !== false) {
                if (!count($this->header)) {
                    $row[0] = preg_replace('/^\x{FEFF}/u', '', $row[0]);
                    $this->header = $this->readRow($row[0]);
                    continue;
                }
                yield $this->readRow($row[0]);
            }
        } finally {
            fclose($csv);
        }
        return [];
    }

    private function readRow(string $row): array
    {
        return str_getcsv($row, ",", '"', '\\');
    }
}


/**
 * ID /STAG ID
 * Case Number / FATO
 * Date / DIM DATA
 * Block / ZZZ
 * IUCR / DIM IUCR
 * Primary Type / DIM TIPO
 * Description / FATO
 * Location Description // LOCAL
 * Arrest true / false
 * Domestic true / false
 * Beat XXX
 * District XXX
 * Ward XXXX
 * Community Area XXXX
 * FBI Code XXXXX
 * X Coordinate XXXX
 * Y Coordinate XXXX
 * Year /XXX
 * Updated On (DATA)
 * Latitude LOCAL
 * Longitude LOCAL
 * Location (LAT, LONG)

    FATO: CRIME - domestic, arrest, case number
 *  DIMENSÕES:
        LOCAL
 *          lat
 *          long
 *          descrição
 *      DESCRIPTION
 *      DATA DO CASO
 *      DATA UPDATE
 *      TIPO DO CRIME
 *      IUCR
 */

