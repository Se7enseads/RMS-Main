<?php

namespace App\Routes;

use App\Controllers\RoleController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$roleRoutes = new RouteCollection();

$roleRoutes->add('roles_index', new Route('/admin/roles', [
    '_controller' => [RoleController::class, 'index'],
    '_auth' => true,
    '_permission' => 'roles.view',
], methods: ['GET']));

$roleRoutes->add('roles_create', new Route('/admin/roles/create', [
    '_controller' => [RoleController::class, 'create'],
    '_auth' => true,
    '_permission' => 'roles.create',
], methods: ['GET']));

$roleRoutes->add('roles_save', new Route('/admin/roles/create', [
    '_controller' => [RoleController::class, 'save'],
    '_auth' => true,
    '_permission' => 'roles.create',
    '_csrf' => true,
], methods: ['POST']));

$roleRoutes->add('roles_edit', new Route('/admin/roles/edit/{id}', [
    '_controller' => [RoleController::class, 'edit'],
    '_auth' => true,
    '_permission' => 'roles.update',
], methods: ['GET']));

$roleRoutes->add('roles_update', new Route('/admin/roles/edit/{id}', [
    '_controller' => [RoleController::class, 'update'],
    '_auth' => true,
    '_permission' => 'roles.update',
    '_csrf' => true,
], methods: ['POST']));

$roleRoutes->add('roles_deactivate', new Route('/admin/roles/deactivate/{id}', [
    '_controller' => [RoleController::class, 'deactivate'],
    '_auth' => true,
    '_permission' => 'roles.deactivate',
    '_csrf' => true,
], methods: ['POST']));

return $roleRoutes;
