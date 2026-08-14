<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Session;
use App\Core\View;
use App\Repositories\PermissionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

$routes = require __DIR__ . '/../src/routes.php';

// get and match the current HTTP Request to a route from $routes
$request = Request::createFromGlobals();
$context = new RequestContext()->fromRequest($request);
$matcher = new UrlMatcher($routes, $context);

try {
    // get the parameters from the matched route
    $parameters = $matcher->match($request->getPathInfo());

    // TODO: Refactor Auth behavior
    if ($parameters['_auth'] ?? false) {
        Session::start();
        if (!Session::has('user_id')) {
            header('Location: /login');
            return;
        }
    }

    if ($parameters['_permission'] ?? false) {
        Session::start();
        $roleId = (int)Session::get('role_id');
        $permissionRepo = new PermissionRepository();
        if (!$roleId || !$permissionRepo->roleHasPermission($roleId, $parameters['_permission'])) {
            http_response_code(403);
            View::render('errors/403');
            return;
        }
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
} catch (ResourceNotFoundException $e) {
    http_response_code(404);
    View::render('errors/404');
}
