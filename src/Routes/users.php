<?php

use App\Controllers\UserController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();

$routes->add('users_index', new Route('/users', [
  '_controller' => [UserController::class, 'index']
], methods: ['GET']));

$routes->add('users_create', new Route('/users/create', [
  '_controller' => [UserController::class, 'create']
], methods: ['GET']));

return $routes;
