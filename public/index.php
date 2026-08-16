<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Middleware;
use App\Core\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$routes = require __DIR__ . '/../src/routes.php';

date_default_timezone_set("Africa/Nairobi");

// get and match the current HTTP Request to a route from $routes
$request = Request::createFromGlobals();
$context = new RequestContext()->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
    // get the parameters from the matched route
    $parameters = $matcher->match($request->getPathInfo());

    // check if the request has a valid CSRF token
    if (!Middleware::csrf($parameters)) {
        http_response_code(400);
        View::render('errors/400');
        return;
    }

    // check if user is logged in if needed
    if (!Middleware::auth($parameters)) {
        header('Location: /login');
        return;
    }

    // check if user has the right permissions for the current action
    if (!Middleware::permissions($parameters)) {
        http_response_code(403);
        View::render('errors/403');
        return;
    }

    // get the controller class and method from the parameters
    [$controllerClass, $method] = $parameters['_controller'];

    // get the args passed in the url
    $args = array_filter(
        $parameters,
        fn($key) => !str_starts_with($key, '_'),
        ARRAY_FILTER_USE_KEY,
    );

    // instantiate and call controller
    $controller = new $controllerClass();
    $controller->$method(...$args);
} catch (MethodNotAllowedException $e) {
    http_response_code(405);
    View::render('errors/404');
} catch (ResourceNotFoundException $e) {
    http_response_code(404);
    View::render('errors/404');
}
