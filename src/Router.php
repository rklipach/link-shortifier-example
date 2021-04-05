<?php

namespace App;

class Router {

    protected static $get = [];
    protected static $post = [];

    /**
     * @param string $action
     * @param string $callbackDefinition
     */
    public static function get($action, $callbackDefinition) {
        $action = trim($action, '/');
        static::$get[$action] = $callbackDefinition;
    }

    /**
     * @param string $action
     * @param string $callbackDefinition
     */
    public static function post($action, $callbackDefinition) {
        $action = trim($action, '/');
        static::$post[$action] = $callbackDefinition;
    }

    /**
     * @param $action string
     */
    public static function dispatch($action) {
        $action = trim($action, '/');

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $callback = static::$get[$action] ??
                static::$get['?'] ??
                function() {
                    echo 'not found';
                };
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $callback = static::$post[$action];
        } else {
            throw new \Exception('Not supported', 500);
        }

        echo call_user_func($callback);
    }
}