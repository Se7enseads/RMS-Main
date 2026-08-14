<?php

namespace App\Routes;

use App\Controllers\MenuItemController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$itemRoutes = new RouteCollection();

$itemRoutes->add('items_index', new Route('/items', [
    '_controller' => [MenuItemController::class, 'index'],
    '_auth' => true,
    '_permission' => 'menu.view',
], methods: ['GET']));

$itemRoutes->add('items_create', new Route('/items/create', [
    '_controller' => [MenuItemController::class, 'create'],
    '_auth' => true,
    '_permission' => 'menu.create',
], methods: ['GET']));

$itemRoutes->add('items_save', new Route('/items/create', [
    '_controller' => [MenuItemController::class, 'save'],
    '_auth' => true,
    '_permission' => 'menu.create',
], methods: ['POST']));

return $itemRoutes;
