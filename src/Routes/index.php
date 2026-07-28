<?php

namespace App\Routes;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use App\Controllers\IndexController;

$indexRoutes = new RouteCollection();

$indexRoutes->add('home', new Route('/', [
    '_controller' => [IndexController::class, 'index'],
], methods: ['GET']));

return $indexRoutes;
