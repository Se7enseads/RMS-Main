<?php

namespace App\Routes;

use App\Controllers\CloseDayController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$dayRoutes = new RouteCollection();

$dayRoutes->add('close_day_form', new Route('/admin/close-day', [
    '_controller' => [CloseDayController::class, 'form'],
    '_auth' => true,
    '_permission' => 'day.close',
], methods: ['GET']));

$dayRoutes->add('close_day_run', new Route('/admin/close-day', [
    '_controller' => [CloseDayController::class, 'run'],
    '_auth' => true,
    '_csrf' => true,
    '_permission' => 'day.close',
], methods: ['POST']));

return $dayRoutes;