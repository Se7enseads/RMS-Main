<?php

namespace App\Routes;

use App\Controllers\AuditLogController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$logRoutes = new RouteCollection();

$logRoutes->add('logs_index', new Route('/admin/logs', [
    '_controller' => [AuditLogController::class, 'index'],
    '_auth' => true,
    '_permission' => 'log.view',
], methods: ['GET']));

return $logRoutes;
