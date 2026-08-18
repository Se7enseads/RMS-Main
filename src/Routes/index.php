<?php

namespace App\Routes;

use App\Controllers\IndexController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$indexRoutes = new RouteCollection();

$indexRoutes->add('home', new Route('/admin', [
    '_controller' => [IndexController::class, 'index'],
    '_auth' => true,
    '_permission' => 'dashboard.view',
], methods: ['GET']));

$indexRoutes->add('home_redirect', new Route('/', [
    '_controller' => [IndexController::class, 'redirectToAdmin'],
    '_auth' => true,
], methods: ['GET']));

return $indexRoutes;
