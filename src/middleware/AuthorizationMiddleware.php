<?php

namespace Middleware;

use DI\Container;
use Slim\Psr7\Response;

class AuthorizationMiddleware
{
    private Container $container;

    function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $handler)
    {
        $currentUser = $_SESSION['user']->getUsername();

        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $uri);
        $societyID = explode("?", $uri[2])[0];


        $userService = $this->container->get("userService");
        $societyService = $this->container->get("societyService");
        $user = $userService->getUserByUsername($currentUser);
        $society = $societyService->getSociety($societyID);

        $societies = $user->getSocieties();
        if(!$societies->isEmpty())
        {
            if ($societies->contains($society)) {
                return $handler->handle($request);
            }
        }

        $response = new Response();
        $response = $response->withStatus(403, "Forbidden");
        $body = require __DIR__ . "/../../views/errors/403.view.php";
        $response->getBody()->write("$body");

        return $response;
    }
}