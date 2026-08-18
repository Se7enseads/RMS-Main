<?php

namespace App\Routes;

use App\Controllers\StoreController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$storeRoutes = new RouteCollection();

$storeRoutes->add('store_index', new Route('/store', [
    '_controller' => [StoreController::class, 'index'],
    '_auth' => true,
    '_permission' => 'inventory.view',
], methods: ['GET']));

$storeRoutes->add('store_create', new Route('/store/inventory/create', [
    '_controller' => [StoreController::class, 'create'],
    '_auth' => true,
    '_permission' => 'inventory.create',
], methods: ['GET']));

$storeRoutes->add('store_save', new Route('/store/inventory/create', [
    '_controller' => [StoreController::class, 'save'],
    '_auth' => true,
    '_permission' => 'inventory.create',
    '_csrf' => true,
], methods: ['POST']));

$storeRoutes->add('store_edit', new Route('/store/inventory/edit/{id}', [
    '_controller' => [StoreController::class, 'edit'],
    '_auth' => true,
    '_permission' => 'inventory.update',
], methods: ['GET']));

$storeRoutes->add('store_update', new Route('/store/inventory/edit/{id}', [
    '_controller' => [StoreController::class, 'update'],
    '_auth' => true,
    '_permission' => 'inventory.update',
    '_csrf' => true,
], methods: ['POST']));

$storeRoutes->add('store_deactivate', new Route('/store/inventory/deactivate/{id}', [
    '_controller' => [StoreController::class, 'deactivate'],
    '_auth' => true,
    '_permission' => 'inventory.deactivate',
    '_csrf' => true,
], methods: ['POST']));

$storeRoutes->add('store_stock_form', new Route('/store/inventory/stock/{id}', [
    '_controller' => [StoreController::class, 'stockForm'],
    '_auth' => true,
    '_permission' => 'inventory.update',
], methods: ['GET']));

$storeRoutes->add('store_stock_save', new Route('/store/inventory/stock/{id}', [
    '_controller' => [StoreController::class, 'stockSave'],
    '_auth' => true,
    '_permission' => 'inventory.update',
    '_csrf' => true,
], methods: ['POST']));

return $storeRoutes;