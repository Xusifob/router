<?php

use \Xusifob\Router;
use \Acme\Services\DummySecurity;


if(!file_exists(__DIR__ . '/../vendor/autoload.php')){
    die('Did you install the dependencies running composer install ?');
}

$loader = require_once "../vendor/autoload.php";
if($loader instanceof \Composer\Autoload\ClassLoader){
    $loader->add('Xusifob', __DIR__ . '/../src');
}


if($loader instanceof \Composer\Autoload\ClassLoader){
    $loader->add('Acme', __DIR__ . '/src');
}


$security = new DummySecurity();

// An array of data to send to the controllers
$config = array();

try {
    $router = new Router($_GET['url'], __DIR__ . "/config/routes.json", $security);
    $router->run($config);
}catch (\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $e) {
    // @TODO Display 404 Page FOUND
    echo "404 Page not found";
}
catch (\Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException $e) {
    // @TODO Display Access Denied Page
    echo "401 : Unauthorized";
}