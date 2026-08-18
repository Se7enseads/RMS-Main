<?php

namespace App\Routes;

use App\Controllers\CategoryController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$categoryRoutes = new RouteCollection();

$categoryRoutes->add('categories_index', new Route('/admin/categories', [
    '_controller' => [CategoryController::class, 'index'],
    '_auth' => true,
    '_permission' => 'categories.view',
], methods: ['GET']));

$categoryRoutes->add('categories_create', new Route('/admin/categories/create', [
    '_controller' => [CategoryController::class, 'create'],
    '_auth' => true,
    '_permission' => 'categories.create',
], methods: ['GET']));

$categoryRoutes->add('categories_save', new Route('/admin/categories/create', [
    '_controller' => [CategoryController::class, 'save'],
    '_auth' => true,
    '_permission' => 'categories.create',
    '_csrf' => true,
], methods: ['POST']));

$categoryRoutes->add('categories_edit', new Route('/admin/categories/edit/{id}', [
    '_controller' => [CategoryController::class, 'edit'],
    '_auth' => true,
    '_permission' => 'categories.update',
], methods: ['GET']));

$categoryRoutes->add('categories_update', new Route('/admin/categories/edit/{id}', [
    '_controller' => [CategoryController::class, 'update'],
    '_auth' => true,
    '_permission' => 'categories.update',
    '_csrf' => true,
], methods: ['POST']));

$categoryRoutes->add('categories_deactivate', new Route('/admin/categories/deactivate/{id}', [
    '_controller' => [CategoryController::class, 'deactivate'],
    '_auth' => true,
    '_permission' => 'categories.deactivate',
    '_csrf' => true,
], methods: ['POST']));

return $categoryRoutes;