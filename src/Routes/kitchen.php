<?php

namespace App\Routes;

use App\Controllers\KitchenController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$kitchenRoutes = new RouteCollection();

$kitchenRoutes->add('kitchen_index', new Route('/kitchen', [
    '_controller' => [KitchenController::class, 'index'],
    '_auth' => true,
    '_permission' => 'kitchen.view',
], methods: ['GET']));

$kitchenRoutes->add('kitchen_serve', new Route('/kitchen/serve/{id}', [
    '_controller' => [KitchenController::class, 'serve'],
    '_auth' => true,
    '_permission' => 'kitchen.view',
    '_csrf' => true,
], methods: ['POST']));

return $kitchenRoutes;