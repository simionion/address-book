<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Services\ServiceProvider;
use Routes\Router;

$_ENV = Dotenv::createImmutable(__DIR__ . '/../', '.env')->load();
$ServiceProvider = new ServiceProvider($_ENV);
$Router = new Router($ServiceProvider);
$Router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
