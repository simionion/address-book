<?php
declare(strict_types=1);

namespace Routes;

use FastRoute\Dispatcher;
use Services\ServiceProvider;


class Router
{
    private Dispatcher $dispatcher;
    private ServiceProvider $serviceProvider;

    public function __construct(ServiceProvider $serviceProvider)
    {
        $this->serviceProvider = $serviceProvider;
        $this->dispatcher = require __DIR__ . '/../Routes/routes.php';
    }

    public function dispatch(string $httpMethod, string $uri): void
    {
        // Strip query string (?foo=bar) and decode URI
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // Handle 404 Not Found
                http_response_code(404);
                echo '404 Not Found';
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                // Handle 405 Method Not Allowed
                http_response_code(405);
                echo '405 Method Not Allowed';
                break;
            case Dispatcher::FOUND:
                [$controller, $method] = $routeInfo[1];
                $vars = $routeInfo[2];

                // Create the controller using the ServiceProvider
                $controllerInstance = $this->serviceProvider->dice->create($controller);

                // Call the method on the controller
                call_user_func_array([$controllerInstance, $method], $vars);
                break;
        }
    }
}
