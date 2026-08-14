<?php

namespace App\Routes;

use App\Controllers\IndexController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$indexRoutes = new RouteCollection();

$indexRoutes->add('home', new Route('/', [
    '_controller' => [IndexController::class, 'index'],
    '_auth' => true,
    '_permission' => 'dashboard.view',
], methods: ['GET']));

return $indexRoutes;
