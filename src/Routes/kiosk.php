<?php

namespace App\Routes;

use App\Controllers\KioskController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$kioskRoutes = new RouteCollection();

$kioskRoutes->add('kiosk', new Route('/kiosk', [
    '_controller' => [KioskController::class, 'index'],
], methods: ['GET']));

return $kioskRoutes;
