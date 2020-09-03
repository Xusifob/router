<?php

use \Xusifob\Router\Router;
use \Acme\Services\DummySecurity;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\RedirectResponse;

ini_set('display_startup_errors',1);
ini_set('display_errors',1);

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

    $router = new Router($_GET['url'],$security);

    // An array of data to send to the controllers
    $config = array(
        'security' => $security,
        'router' => $router
    );

    $router->run($config);
}catch (NotFoundHttpException $e) {
    $response = new Response($e->getMessage(),Response::HTTP_NOT_FOUND);
}
catch (UnauthorizedHttpException $e) {
    $redirect =  new RedirectResponse($router->generateUrl('app_login'));
    $redirect->send();
    die();
}
catch (AccessDeniedHttpException $e) {
    $response = new Response($e->getMessage(),Response::HTTP_FORBIDDEN);
    $response->send();
    die();
}
catch (BadRequestHttpException $e) {
    $response = new Response($e->getMessage(),Response::HTTP_BAD_REQUEST);
    $response->send();
    die();
}
