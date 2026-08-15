<?php

namespace App;

use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// Import and merge route collections
$routes->addCollection(require __DIR__ . '/Routes/auth.php');
$routes->addCollection(require __DIR__ . '/Routes/index.php');
$routes->addCollection(require __DIR__ . '/Routes/users.php');
$routes->addCollection(require __DIR__ . '/Routes/roles.php');
$routes->addCollection(require __DIR__ . '/Routes/items.php');
$routes->addCollection(require __DIR__ . '/Routes/kitchen.php');
$routes->addCollection(require __DIR__ . '/Routes/kiosk.php');

return $routes;
