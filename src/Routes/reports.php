<?php

namespace App\Routes;

use App\Controllers\ReportsController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$reportRoutes = new RouteCollection();

$reportRoutes->add('reports_index', new Route('/admin/reports', [
    '_controller' => [ReportsController::class, 'index'],
    '_auth' => true,
    '_permission' => 'reports.view',
], methods: ['GET']));

return $reportRoutes;