<?php

namespace App\Routes;

use App\Controllers\KioskController;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$kioskRoutes = new RouteCollection();

$kioskRoutes->add('kiosk_dashboard', new Route('/kiosk', [
    '_controller' => [KioskController::class, 'dashboard'],
    '_auth' => true,
], methods: ['GET']));

$kioskRoutes->add('kiosk_order', new Route('/kiosk/order', [
    '_controller' => [KioskController::class, 'order'],
    '_auth' => true,
], methods: ['GET']));

$kioskRoutes->add('kiosk_place_order', new Route('/kiosk/order', [
    '_controller' => [KioskController::class, 'placeOrder'],
    '_auth' => true,
], methods: ['POST']));

return $kioskRoutes;
