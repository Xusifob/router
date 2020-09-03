<?php

use \Xusifob\Router\Router;
use \Acme\Services\DummySecurity;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use \Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\RedirectResponse;


$config = require 'load.php';

// Create the security service to handle auth/view of the user
$security = new DummySecurity();

try {

    $router = new Router($security);
    // An array of data to send to the controllers
    $config = array(
        'security' => $security,
    );
    $router->run($config);

// File not found, route not found
}catch (NotFoundHttpException $e) {
    $response = new Response($e->getMessage(),Response::HTTP_NOT_FOUND);
    $response->send();
    die();
}
// User is not logged in (401)
catch (UnauthorizedHttpException $e) {
    $redirect =  new RedirectResponse($router->generateUrl('app_login'));
    $redirect->send();
    die();
}
// Access Forbidden (403)
catch (AccessDeniedHttpException $e) {
    $response = new Response($e->getMessage(),Response::HTTP_FORBIDDEN);
    $response->send();
    die();
}
// Bad request
catch (BadRequestHttpException $e) {
    $response = new Response($e->getMessage(),Response::HTTP_BAD_REQUEST);
    $response->send();
    die();
}
