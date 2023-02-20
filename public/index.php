<?php

declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use App\App;
use App\Container;
use App\Router;
use Dotenv\Dotenv;

session_start();

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$container = new Container();
$router = new Router($container);

(new App(
    $container,
    $router,
    [
        'host' => $_ENV['MARIADB_HOST'],
        'username' => $_ENV['MARIADB_USER'],
        'password' => $_ENV['MARIADB_PASSWORD'],
        'database' => $_ENV['MARIADB_DATABASE']
    ],
    [
        'method' => $_SERVER['REQUEST_METHOD'],
        'uri' => $_SERVER['REQUEST_URI']
    ]
))->run();
