<?php

namespace App\Routes;

use App\Controllers\CashierController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$cashierRoutes = new RouteCollection();

$cashierRoutes->add('cashier_index', new Route('/cashier', [
    '_controller' => [CashierController::class, 'index'],
    '_auth' => true,
    '_permission' => 'cashier.view',
], methods: ['GET']));

$cashierRoutes->add('cashier_pay', new Route('/cashier/orders/{id}/pay', [
    '_controller' => [CashierController::class, 'pay'],
    '_auth' => true,
    '_csrf' => true,
    '_permission' => 'cashier.settle',
], methods: ['POST']));

return $cashierRoutes;