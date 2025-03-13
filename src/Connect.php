<?php

namespace Php\DW;

class Connect
{
    private static ?\PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): ?\PDO
    {
        if (self::$instance === null) {
            $dsn = "pgsql:host=banco_de_dados;port=5432;dbname=dw";
            $username = "cascata";
            $password = "root";

            try {
                self::$instance = new \PDO($dsn, $username, $password);

                self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch (\PDOException $exception) {
                dd($exception);
            }
        }
        return self::$instance;
    }
}
