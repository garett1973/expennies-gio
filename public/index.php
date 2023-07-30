<?php
declare(strict_types = 1);
//error_reporting(-1);
//ini_set('display_errors', 'On');
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

use Slim\App;

$container = require __DIR__ . '/../bootstrap.php';
$container->get(App::class)->run();
