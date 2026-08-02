<?php

namespace App\Routes;

use App\Controllers\AuthController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

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
], methods: ['GET'])); // NOTE: should be a POST

return $authRoutes;
