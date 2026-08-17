<?php

namespace App\Routes;

use App\Controllers\BarController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$barRoutes = new RouteCollection();

$barRoutes->add('bar_index', new Route('/bar', [
    '_controller' => [BarController::class, 'index'],
    '_auth' => true,
    '_permission' => 'bar.view',
], methods: ['GET']));

$barRoutes->add('bar_serve', new Route('/bar/serve/{id}', [
    '_controller' => [BarController::class, 'serve'],
    '_auth' => true,
    '_permission' => 'bar.view',
    '_csrf' => true,
], methods: ['POST']));

$barRoutes->add('bar_order', new Route('/bar/order', [
    '_controller' => [BarController::class, 'order'],
    '_auth' => true,
    '_permission' => 'bar.view',
], methods: ['GET']));

$barRoutes->add('bar_place_order', new Route('/bar/order', [
    '_controller' => [BarController::class, 'placeOrder'],
    '_auth' => true,
    '_permission' => 'bar.view',
    '_csrf' => true,
], methods: ['POST']));

return $barRoutes;