<?php

/**
 * Security Service
 */


namespace Xusifob\Router\Services;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Xusifob\Router\Route;

/**
 *
 * This service will be used to check everything linked to logged in user and security.
 *
 * It can return if the user is allowed to get & update an entity, and if the user is currently logged it.
 *
 * Class Security
 * @package Xusifob\Services
 */
abstract class Security
{

    /**
     * Get the current user id
     *
     * @return bool|int
     */
    abstract public function getCurrentUser();


    /**
     * Return if a user is logged in or not
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->getCurrentUser() !== false;
    }

    /**
     * @return string|null
     */
    abstract public function getRole() : ?string;


    /**
     * @param string $role
     * @return bool
     */
    public function isRole(string $role)
    {
        return $this->getRole() === $role;
    }


    /**
     *
     * Return if the user is the owner of the entity and if he is allowed to fetch & update it
     *
     * @param object         $entity     The entity you want to test
     * @param null|int      $user_id    The user you want to test your entity against. default: current user id
     *
     * @return bool
     */
    abstract public function isOwner($entity,$user_id = null);


    /**
     * @param Route     $route      A route
     * @param mixed     $user       A user
     * @return mixed
     */
    abstract public function canView(Route $route,$user = null);


    /**
     * Redirect the user if he is not logged in
     *
     * @param $url
     */
    public function redirectIfNotLoggedIn($url)
    {
        if(!self::isLoggedIn()) {
            $response = new RedirectResponse($url);
            $response->send();
        }
    }

}
