<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class Kernel
{
    public static function handle(Request $request): Response
    {
        self::syncGlobals($request);

        $routes = require __DIR__ . '/../routes.php';
        $context = new RequestContext()->fromRequest($request);
        $matcher = new UrlMatcher($routes, $context);

        ob_start();
        http_response_code(200);

        try {
            // get the parameters from the matched route
            $parameters = $matcher->match($request->getPathInfo());

            // check if the request has a valid CSRF token
            if (!Middleware::csrf($parameters)) {
                Session::destroy();
                http_response_code(400);
                View::render('errors/400');
            } elseif (!Middleware::auth($parameters)) {
                Session::destroy();
                Redirect::to('/login');
            } elseif (!Middleware::permissions($parameters)) {
                http_response_code(403);
                View::render('errors/403');
            } else {
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
            }
        } catch (MethodNotAllowedException $e) {
            http_response_code(405);
            View::render('errors/404');
        } catch (ResourceNotFoundException $e) {
            http_response_code(404);
            View::render('errors/404');
        }

        $output = ob_get_clean();

        $status = http_response_code();
        if ($status === false || $status === 0) {
            $status = 200;
        }

        $response = new Response($output, $status);

        if (Redirect::$lastUrl !== null) {
            $response->setStatusCode(302);
            $response->headers->set('Location', Redirect::$lastUrl);
        }

        Redirect::clear();

        return $response;
    }

    /**
     * Controllers and views read PHP superglobals directly; keep them in sync
     * with the Request so the same code path runs in tests and via the web.
     */
    private static function syncGlobals(Request $request): void
    {
        $_GET = $request->query->all();
        $_POST = $request->request->all();
        $_SERVER['REQUEST_URI'] = $request->getPathInfo();
        $_SERVER['REQUEST_METHOD'] = $request->getMethod();
    }
}