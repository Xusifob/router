<?php

/**
 * Interface for Controllers
 */

namespace Xusifob\Controller;



use Xusifob\Services\Security;

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
     * @param $string
     * @return bool|mixed
     */
    public function getData($string)
    {
        if(isset($this->data[$string])) {
            return $this->data[$string];
        }

        return false;
    }




}