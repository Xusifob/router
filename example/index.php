<?php

use \Xusifob\Router;
use \Acme\Services\DummySecurity;


if(!file_exists(__DIR__ . '/vendor/autoload.php')){
    die('Did you install the dependencies running composer install ?');
}

$loader = require_once "../vendor/autoload.php";

// Load all the Acme Dummy classes
if($loader instanceof \Composer\Autoload\ClassLoader){
    $loader->add('Acme', __DIR__ . '/src');
}


// Create the security service to handle auth/view of the user
$security = new DummySecurity();



try {
    $router = new Router($_GET['url'], __DIR__ . "/config/routes.json", $security);

    // An array of data to send to the controllers
    $config = array(
        'security' => $security,
        'router' => $router
    );

    $router->run($config);
}catch (\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $e) {
    $response = new \Symfony\Component\HttpFoundation\Response("404 Page not found",\Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
}
catch (\Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException $e) {
    $redirect =  new \Symfony\Component\HttpFoundation\RedirectResponse($router->generateUrl('home'));
    $redirect->send();
    die();
}