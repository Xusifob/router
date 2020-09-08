<?php

/**
 * Interface for Controllers
 */

namespace Xusifob\Router\Controller;



use Symfony\Component\HttpFoundation\Request;
use Xusifob\Router\Route;
use Xusifob\Router\Router;
use Xusifob\Router\Services\Security;

/**
 *
 * Interface for every controller.
 *
 * Interface Controller
 * @package SmartPage\Controller
 */
abstract class Controller
{


    /**
     * @var array
     */
    protected $data = array();


    /**
     * Controller constructor.
     * @param array     $data   An array of all the data you want to get
     */
    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * @return Security
     */
    public function getSecurity() : Security
    {
        return $this->getData('security');
    }

    /**
     * @return Router
     */
    public function getRouter() : Router
    {
        return $this->getData('router');
    }

    /**
     * @return Request|null
     */
    public function getRequest() : ?Request
    {
        return $this->getData('request');
    }


    /**
     * @param $string
     * @return bool|mixed
     */
    public function getData($string)
    {
        if(isset($this->data[$string])) {
            return $this->data[$string];
        }

        return null;
    }

}
