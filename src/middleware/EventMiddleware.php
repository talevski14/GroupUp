<?php

namespace Middleware;

use DI\Container;
use Slim\Psr7\Response;

class EventMiddleware
{
    private Container $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $handler)
    {
        $database = $this->container->get("db");
        $currentUser = $_SESSION['user']->getUsername();

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $uri);
        $eventID = $uri[4];

        $eventService = $this->container->get("eventService");
        $userService = $this->container->get("userService");

        $event = $eventService->getEventById($eventID);
        $user = $userService->getUserByUsername($currentUser);

        if($event->getCreator() != $user) {
            $response = new Response();
            $response = $response->withStatus(403, "Forbidden");
            $body = require __DIR__ . "/../../views/errors/403-event.view.php";
            $response->getBody()->write("$body");
            return $response;
        }
        return $handler->handle($request);
    }
}