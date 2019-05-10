<?php
// src/Acme/DummyController.php

namespace Acme\Controller;



use Xusifob\Controller\Controller;

/**
 *
 * Interface for every controller.
 *
 * Interface Controller
 * @package Xusifob\Controller
 */
class DummyController extends Controller
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
        parent::__construct($data);
    }


    /**
     * @param array $matches
     */
    public function test($matches = array())
    {
        var_dump($matches);
        var_dump($this->getSecurity());

        die('You reached the dummy controller');
    }





}