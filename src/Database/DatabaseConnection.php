<?php

namespace App\Database;

use PDO;

class DatabaseConnection {
    /**
     * @var PDO|null
     */
    protected static $instance = null;

    protected function __construct() {
    }

    protected function __clone() {
    }

    /**
     * @return PDO|null
     */
    public static function getInstance() {
        if (is_null(static::$instance)) {
            $dsn = sprintf("%s:dbname=%s;host=%s;port=%s", DB_TYPE, DB_NAME, DB_HOST, DB_PORT);
            $user = DB_USER;
            $password = DB_PASSWORD;

            static::$instance = new PDO($dsn, $user, $password);
        }

        return static::$instance;
    }
}