<?php

namespace App\Routes;

use App\Controllers\UserController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$userRoutes = new RouteCollection();

$userRoutes->add('users_index', new Route('/users', [
    '_controller' => [UserController::class, 'index'],
    '_auth' => true,
], methods: ['GET']));

$userRoutes->add('users_create', new Route('/users/create', [
    '_controller' => [UserController::class, 'create'],
    '_auth' => true,
], methods: ['GET']));

$userRoutes->add('users_save', new Route('/users/create', [
    '_controller' => [UserController::class, 'save'],
    '_auth' => true,
], methods: ['POST']));

return $userRoutes;
