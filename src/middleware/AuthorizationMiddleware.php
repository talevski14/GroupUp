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
//        $database = $this->container->get("db");
//        $currentUser = $_SESSION['user']['id'];
//
//        $uri = $_SERVER['REQUEST_URI'];
//        $uri = explode('/', $uri);
//        $societyID = explode("?", $uri[2])[0];
//
//
//        $user = $database->query("select * from users where id = :id", [
//            ":id" => $currentUser
//        ])->find();
//
//        $societies = $user['societies'];
//        if(isset($societies))
//        {
//            $societies = explode(";", $societies);
//            array_shift($societies);
//
//            if (in_array($societyID, $societies)) {
//                return $handler->handle($request);
//            }
//        }
//
//        $response = new Response();
//        $response = $response->withStatus(403, "Forbidden");
//        $body = require __DIR__ . "/../../views/errors/403.view.php";
//        $response->getBody()->write("$body");
//
//        return $response;
        return $handler->handle($request);
    }
}