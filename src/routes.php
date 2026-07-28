<?php

namespace App;

use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// Import and merge route collections
$routes->addCollection(require __DIR__ . '/Routes/index.php');
$routes->addCollection(require __DIR__ . '/Routes/users.php');

return $routes;
