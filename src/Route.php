<?php

/**
 * APi Route File
 */


namespace Xusifob;


use http\Client\Response;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 *
 * This is a route for the API
 *
 * @see https://www.grafikart.fr/tutoriels/php/router-628
 *
 * Class Route
 * @package Xusifob
 */
class Route implements \JsonSerializable {

	/**
	 * The host requested
	 *
	 * @var string
	 */
	private $host = null;

	/**
	 * The Path of the route
	 *
	 * @var string
	 */
	private $path;

	/**
	 *
	 * The callback class when the route is matched
	 *
	 * @var string
	 */
	private $class;

	/**
	 * The method in the callback class when the route is matched
	 *
	 * @var string
	 */
	private $method;


	/**
	 *
	 * The type of Route it is (GET, PUT, POST, DELETE...)
	 *
	 * @var string
	 */
	private $type;


	/**
	 * @var bool|false
	 */
	private $is_visible = true;


	/**
	 *
	 * Array of matching routes
	 *
	 * @var array
	 */
	private $matches = array();


	/**
	 *
	 * All the parameters that are found in the route
	 *
	 * @var array
	 */
	private $params = array();


    /**
     * @var array
     */
	private $config = array();


	/**
	 * Route constructor.
	 *
	 * Instancing a route for the Router
	 *
	 *
	 * @param $host         string          The host of the route
	 * @param $type         string          The type of call (PUT, GET, POST, DELETE)
	 * @param $path         string          The path of the route. Parameters are with :parameter
	 * @param $class        string          The Class of the callback function
	 * @param $method       string          The callback method inside the class
	 * @param $is_visible   bool|false      If the route is visible or you need to be logged in
	 * @param $config       array           Some configuration within the route that may be useful
	 */
	public function __construct($host,$type,$path,$class,$method,$is_visible = false,$config = array()){
		$this->host = $host;
		$this->path = trim($path, '/');
		$this->class = $class;
		$this->type = $type;
		$this->method = $method;
		$this->is_visible = $is_visible;
		$this->config = $config;

	}




	/**
	 *
	 * Return if a parameter is matching with parameters
	 *
	 * @param $match
	 *
	 * @return string
	 */
	private function paramMatch($match){
		if(isset($this->params[$match[1]])){
			return '(' . $this->params[$match[1]] . ')';
		}
		return '([^\/]+)';
	}



	/**
	 * Return if the route is matching the current path
	 *
	 * @param $url  string      The current path
	 *
	 * @return bool
	 */
	public function match($url){

		if($this->host !== null && $this->host !== $_SERVER['HTTP_HOST']) // for our usage, no need for regexp
			return false;

		if(is_array($this->type)) {
		    if(!in_array($_SERVER['REQUEST_METHOD'],$this->type)) {
		        return  false;
            }
        } else {

            if ($this->type !== $_SERVER['REQUEST_METHOD'])
                return false;
        }

		$url = '/' .trim($url, '/');
		$path = preg_replace_callback('#:([\w]+)#', [$this, 'paramMatch'], $this->path);
		$regex = "#^\/$path$#i";
		
        if(!preg_match($regex, $url, $matches)){
			return false;
		}
        
		// Get the parameters id
		preg_match_all('/(\/)?:.+\/?/i',$this->path,$parameters);


		array_shift($matches);

		$match_sort = array();

		// Get the parameters from the path
		if(isset($parameters[0][0])) {
			$keys = str_replace( '/', '', $parameters[0][0] );

			$keys = trim( $keys, ':' );

			$keys = explode( ':', $keys );

			// Create the associative array
			foreach($matches as  $key => $match){

				$match_sort[$keys[$key]] = $match;
			}
		}else{
			$match_sort = $matches;
		}


		$this->matches = $match_sort;

		return true;
	}


	/**
	 *
	 * Return if the route is visible or hidden behind a login
	 *
	 * @return bool|false
	 */
	public function isVisible()
	{
		return $this->is_visible;
	}



	/**
	 *
	 * Call the callback of the Route when this one is matched
	 *
	 * @param array     $data   An array of information that must be passed to the controller. It can contain an EntityManager
	 *
	 * @return Response
	 */
	public function call($data){

	    $className = $this->class;

	    $class = new $className($data);
	    
		return call_user_func_array(array($class, $this->method), array($this->matches));
	}


    /**
     *
     * Generate the Url for the route with the designated parameters
     *
     * @param $params array
     * @return mixed|string
     */
	public function generateUrl($params = array())
    {
        $_route = $this->path;

        if(empty($params)) {
            return $_route;
        }



        foreach($params as $key =>  $param) {
            $_route = str_replace(':' . $key,$param,$_route);
        }

        return $_route;
    }


    /**
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
    


    public function jsonSerialize()
    {
        return array(
            'method' => $this->method,
            'type' => $this->type
        );
    }


}