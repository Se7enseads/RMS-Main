<?php

namespace App\Routes;

use App\Controllers\UserController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$userRoutes = new RouteCollection();

$userRoutes->add('users_index', new Route('/users', [
    '_controller' => [UserController::class, 'index'],
    '_auth' => true,
    '_permission' => 'users.view',
], methods: ['GET']));

$userRoutes->add('users_create', new Route('/users/create', [
    '_controller' => [UserController::class, 'create'],
    '_auth' => true,
    '_permission' => 'users.create',
], methods: ['GET']));

$userRoutes->add('users_save', new Route('/users/create', [
    '_controller' => [UserController::class, 'save'],
    '_auth' => true,
    '_permission' => 'users.create',
    '_csrf' => true,
], methods: ['POST']));

$userRoutes->add('users_edit', new Route('/users/update/{id}', [
    '_controller' => [UserController::class, 'edit'],
    '_auth' => true,
    '_permission' => 'users.update',
], methods: ['GET']));

$userRoutes->add('users_update', new Route('/users/update/{id}', [
    '_controller' => [UserController::class, 'update'],
    '_auth' => true,
    '_permission' => 'users.update',
    '_csrf' => true,
], methods: ['POST']));

$userRoutes->add('users_deactivate', new Route('/users/deactivate/{id}', [
    '_controller' => [UserController::class, 'deactivate'],
    '_auth' => true,
    '_permission' => 'users.deactivate',
    '_csrf' => true,
], methods: ['POST']));

return $userRoutes;
