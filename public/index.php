<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use App\Core\View;

// 1. Load the routes
$routes = require __DIR__ . '/../src/routes.php';

// 2. Match the current HTTP Request to a route
$request = Request::createFromGlobals();
$context = (new RequestContext())->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
  // get the parameters from the matched route
  $parameters = $matcher->match($request->getPathInfo());

  // TODO: add Auth and RBAC middleware

  // get the controller class and method from the parameters
  [$controllerClass, $method] = $parameters['_controller'];

  // get the args passed in the url
  $args = array_filter(
    $parameters,
    fn($key) => !str_starts_with($key, '_'),
    ARRAY_FILTER_USE_KEY
  );

  // instantiate and call controller
  $controller = new $controllerClass();
  $controller->$method(...$args);
} catch (ResourceNotFoundException $e) {
  http_response_code(404);
  View::render('errors/404');
}
