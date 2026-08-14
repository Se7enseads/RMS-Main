<?php

namespace App\Routes;

use App\Controllers\RoleController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$roleRoutes = new RouteCollection();

$roleRoutes->add('roles_index', new Route('/roles', [
    '_controller' => [RoleController::class, 'index'],
    '_auth' => true,
    '_permission' => 'roles.view',
], methods: ['GET']));

$roleRoutes->add('roles_create', new Route('/roles/create', [
    '_controller' => [RoleController::class, 'create'],
    '_auth' => true,
    '_permission' => 'roles.create',
], methods: ['GET']));

$roleRoutes->add('roles_save', new Route('/roles/create', [
    '_controller' => [RoleController::class, 'save'],
    '_auth' => true,
    '_permission' => 'roles.create',
], methods: ['POST']));

return $roleRoutes;
