<?php

namespace App\Routes;

use App\Controllers\MenuItemController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$itemRoutes = new RouteCollection();

$itemRoutes->add('items_index', new Route('/admin/items', [
    '_controller' => [MenuItemController::class, 'index'],
    '_auth' => true,
    '_permission' => 'menu.view',
], methods: ['GET']));

$itemRoutes->add('items_create', new Route('/admin/items/create', [
    '_controller' => [MenuItemController::class, 'create'],
    '_auth' => true,
    '_permission' => 'menu.create',
], methods: ['GET']));

$itemRoutes->add('items_save', new Route('/admin/items/create', [
    '_controller' => [MenuItemController::class, 'save'],
    '_auth' => true,
    '_permission' => 'menu.create',
    '_csrf' => true,
], methods: ['POST']));

$itemRoutes->add('items_edit', new Route('/admin/items/edit/{id}', [
    '_controller' => [MenuItemController::class, 'edit'],
    '_auth' => true,
    '_permission' => 'menu.update',
], methods: ['GET']));

$itemRoutes->add('items_update', new Route('/admin/items/edit/{id}', [
    '_controller' => [MenuItemController::class, 'update'],
    '_auth' => true,
    '_permission' => 'menu.update',
    '_csrf' => true,
], methods: ['POST']));

$itemRoutes->add('items_deactivate', new Route('/admin/items/deactivate/{id}', [
    '_controller' => [MenuItemController::class, 'deactivate'],
    '_auth' => true,
    '_permission' => 'menu.deactivate',
    '_csrf' => true,
], methods: ['POST']));

return $itemRoutes;
