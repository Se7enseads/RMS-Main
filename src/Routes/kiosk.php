<?php

namespace App\Routes;

use App\Controllers\KioskController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$kioskRoutes = new RouteCollection();

$kioskRoutes->add('kiosk_dashboard', new Route('/kiosk', [
    '_controller' => [KioskController::class, 'dashboard'],
    '_auth' => true,
], methods: ['GET']));

$kioskRoutes->add('kiosk_order', new Route('/kiosk/order', [
    '_controller' => [KioskController::class, 'order'],
    '_auth' => true,
], methods: ['GET']));

$kioskRoutes->add('kiosk_payments', new Route('/kiosk/payments', [
    '_controller' => [KioskController::class, 'payments'],
    '_auth' => true,
], methods: ['GET']));

// TODO: Add automatic log out
$kioskRoutes->add('kiosk_place_order', new Route('/kiosk/order', [
    '_controller' => [KioskController::class, 'placeOrder'],
    '_auth' => true,
    '_csrf' => true,
], methods: ['POST']));

$kioskRoutes->add('kiosk_bill', new Route('/kiosk/order/{id}/bill', [
    '_controller' => [KioskController::class, 'bill'],
    '_auth' => true,
], methods: ['GET']));

return $kioskRoutes;
