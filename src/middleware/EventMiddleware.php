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
        $currentUser = $_SESSION['user']['id'];

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $uri);
        $eventID = $uri[4];

        $eventCreator = $database->query("select * from events where id = :event", [
            ":event" => $eventID
        ])->find()['creator'];

        if($eventCreator != $currentUser) {
            $response = new Response();
            $response = $response->withStatus(403, "Forbidden");
            $body = require __DIR__ . "/../../views/errors/403-event.view.php";
            $response->getBody()->write("$body");
            return $response;
        }
        return $handler->handle($request);
    }
}