<?php

use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// Import and merge route collections
$routes->addCollection(require __DIR__ . '/Routes/users.php');

return $routes;
