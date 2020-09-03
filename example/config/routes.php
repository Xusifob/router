<?php

use Acme\Controller\DummyController;

return array (
    'path' => '/',
    'name' => 'app',
    'routes' =>
        array (
            'home' =>
                array (
                    'path' => '/',
                    'type' => 'GET',
                    'class' => DummyController::class,
                    'method' => 'test',
                    'config' =>
                        array (
                            'foo' => 'bar',
                            'bar' => 'foo',
                        ),
                ),
            'login' =>
                array (
                    'path' => '/',
                    'type' => 'GET',
                    'class' => DummyController::class,
                    'method' => 'login',
                    'visible' => true,
                    'config' =>
                        array (
                            'foo' => 'bar',
                            'bar' => 'foo',
                        ),
                ),
            'dummy' =>
                array (
                    'path' => 'dummy/:id',
                    'type' => 'POST',
                    'class' => 'DummyController',
                    'method' => 'dummy',
                ),
            'sub_path' =>
                array (
                    'path' => 'subpath/',
                    'routes' =>
                        array (
                            'test' =>
                                array (
                                    'path' => 'test/',
                                    'type' => 'GET',
                                    'class' => 'DummyController',
                                    'method' => 'subPathTest',
                                ),
                            'dummy' =>
                                array (
                                    'path' => 'dummy/:id',
                                    'type' => 'POST',
                                    'class' => 'DummyController',
                                    'method' => 'subPathDummy',
                                ),
                            'sub_path' =>
                                array (
                                    'namespace' => 'SubPath',
                                    'path' => 'subpath/',
                                    'routes' =>
                                        array (
                                            'test' =>
                                                array (
                                                    'path' => 'test/',
                                                    'type' => 'GET',
                                                    'class' => 'DummyController',
                                                    'method' => 'subPathTest',
                                                ),
                                            'dummy' =>
                                                array (
                                                    'path' => 'dummy/:id',
                                                    'type' => 'POST',
                                                    'class' => 'DummyController',
                                                    'method' => 'subPathDummy',
                                                ),
                                        ),
                                ),
                        ),
                ),
        ),
);
