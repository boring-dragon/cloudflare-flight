<?php
require 'vendor/autoload.php';
require 'routes.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


Flight::start();
