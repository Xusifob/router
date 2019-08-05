<?php

/**
 * Router File
 */


namespace Xusifob;

use http\Exception\BadQueryStringException;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Xusifob\Services\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * This is the router class, the router will get the current data from the url and call the correct Controller
 *
 * @see https://www.grafikart.fr/tutoriels/php/router-628
 *
 * Class Router
 * @package SmartPage
 */
class Router {


    /**
     * @var string
     */
    private $url;

    /**
     * @var Route[]
     */
    private $routes = array();


    /**
     * @var Security
     */
    private $securityService;



    /**
     * Router constructor.
     *
     * @param string            $url                The Current URL to test in our router
     * @param string            $config             Path to the JSON configuration file
     * @param Security          $securityService    The service used by the router to handle security
     */
    public function __construct($url,$config,Security $securityService) {


        $this->securityService = $securityService;


        // Set the url
        $this->url = $url;

        $this->loadRoutes($config);


    }




    /**
     *
     * Add a route inside the router. This route will be matchable afterwards
     *
     * @param string    $name           The route name
     * @param array     $routeConfig    Array of route information
     *
     * @return Route
     */
    public function addRoute($name,$routeConfig) : Route
    {

        unset($routeConfig['routes']);

        $r = $this->createRoute($routeConfig);

        $this->routes[$name] = $r;


        return $r;
    }






    /**
     *
     * Create a route Object
     *
     * @param $route
     *
     * @return Route
     */
    public function createRoute($route) : Route
    {

        if(isset($route['namespace'])) {
            $route['class'] = $route['namespace'] . "\\Controller\\{$route['class']}";
        }

        $config = isset($route['config']) ? $route['config'] : array();

        return new Route(
            isset($route['host']) ? $route['host'] : null,
            $route['type'],
            $route['path'],
            $route['class'],
            $route['method'],
            (isset($route['visible']) && $route['visible']),
            $config
        );
    }



    /**
     *
     * Run the router. It will check the current url over all the routes available and call one if it matches
     *
     * It will also send the response from the controller
     *
     * @param $data
     * @throws BadQueryStringException
     * @throws AccessDeniedException
     * @throws FileNotFoundException
     * @throws AccessDeniedException
     */
    public function run($data){


        if(!is_array($this->routes)){
            throw new BadQueryStringException("Routes array cannot be empty");
        }


        foreach($this->getRoutes() as $key =>  $route){

            /** @var route Route */
            if($route->match($this->url)){

                if(!$route->isVisible()) {
                    if(!$this->securityService->isLoggedIn()) {
                        throw new AccessDeniedException("Accesss Denied");
                    }

                    if(!$this->securityService->canView($route)) {
                        throw new AccessDeniedException("Access Denied");
                    }
                }

                
                $response =  $route->call(array_merge(array('security' => $this->securityService),$data));

                
                if(!$response instanceof Response) {
                    throw new \Exception(sprintf("Method %s does not return an instance of Response, %s given",$route->getMethod(),$response));
                }

                $response->send();
                return;
            }
        }

        throw new FileNotFoundException("No route found");
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param Route[] $routes
     */
    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }






    /**
     *
     * This allows the user to generate a route using its name
     *
     * @param $route
     * @param array $params
     *
     * @return mixed
     *
     * @throws FileNotFoundException
     */
    public function generateUrl($route, $params = array())
    {


        if(!isset($this->routes[$route])) {
            throw new FileNotFoundException(sprintf('La route %s n\'existe pas',$route));
        }

        $_route = $this->routes[$route];

        return $_route->generateUrl($params);

    }


    /**
     *
     * Launch the configuration of the routes
     *
     * @param $configPath string  the path to the config file
     *
     *
     * @throws BadQueryStringException
     *
     */
    protected function loadRoutes($configPath)
    {

        if(!file_exists($configPath)) {
            throw new FileNotFoundException($configPath);
        }

        $config = json_decode(file_get_contents($configPath),true);

        if(empty($config) || !isset($config['routes'])) {
            throw new BadQueryStringException();
        }


        $parent = array_merge(array(
            "namespace" => null,
            "path" => "",
            "name" => "",
        ),$config);

        unset($parent['routes']);

        $this->configureRoutes($parent,$config['routes']);

    }


    /**
     * @param $parent
     * @param array $routesConfig
     */
    protected function configureRoutes($parent,$routesConfig = array())
    {


        /**
         * @var string  $name           The name of the route
         * @var array   $routeConfig    An array of config for the route
         */
        foreach($routesConfig as $name => $routeConfig){

            // Concatenate path with parent
            $routeConfig['path'] = $parent['path'] . $routeConfig['path'];

            // Concatenate name with parent
            if(isset($parent['name'])) {
                $name = trim($parent['name'] . '_' . $name, '_');
            }

            $routeConfig['name'] = $name;

            // Set default namespace if not set
            if(!isset($routeConfig['namespace'])) {
                $routeConfig['namespace'] = $parent['namespace'];
            }

            // Add the route if it has the correct infos
            if(isset($routeConfig['class']) && isset($routeConfig['method'])) {
                $this->addRoute($name,$routeConfig);
            }

            // Set sub routes
            if(isset($routeConfig['routes'])) {
                $this->configureRoutes($routeConfig,$routeConfig['routes']);
            }

        }

    }


}