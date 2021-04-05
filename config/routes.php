<?php

use App\Router;

Router::get('/', 'App\Controllers\AppController::index');
Router::get('/result', 'App\Controllers\AppController::renderResult');
Router::get('/?', 'App\Controllers\AppController::redirectFromShortified');

Router::post('/', 'App\Controllers\AppController::shortify');