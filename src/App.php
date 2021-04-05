<?php

namespace App;

class App {
    public static function start() {
        require_once 'config/db.php';
        require_once 'config/routes.php';
        $action = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        Router::dispatch($action);
    }
}