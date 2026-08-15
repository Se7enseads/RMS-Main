<?php

namespace App\Routes;

use App\Controllers\AuthController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$authRoutes = new RouteCollection();

$authRoutes->add('login_form', new Route('/login', [
    '_controller' => [AuthController::class, 'loginForm'],
], methods: ['GET']));

$authRoutes->add('login', new Route('/login', [
    '_controller' => [AuthController::class, 'login'],
], methods: ['POST']));

$authRoutes->add('logout', new Route('/logout', [
    '_controller' => [AuthController::class, 'logout'],
    '_auth' => true,
    '_csrf' => true,
], methods: ['POST']));

return $authRoutes;
