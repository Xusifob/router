<?php

session_start();

use \Composer\Autoload\ClassLoader;

$config = require 'config/config.php';

ini_set('display_startup_errors',$config['env'] === 'dev');
ini_set('display_errors',$config['env'] === 'dev');

ini_set('display_startup_errors',1);
ini_set('display_errors',1);

if(!file_exists(__DIR__ . '/vendor/autoload.php')){
    die('Did you install the dependencies running composer install ?');
}

$loader = require_once "../vendor/autoload.php";

// Load all the Acme Dummy classes
if($loader instanceof ClassLoader){
    $loader->add($config['namespace'], __DIR__ . '/src');
}

define('ROOT_DIR',__DIR__);

return $config;
