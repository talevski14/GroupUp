<?php

namespace Middleware;

use Slim\Psr7\Response;

class MemberMiddleware
{
    public function __invoke($request, $handler)
    {
//        if(!isset($_SESSION['user']))
//        {
//            $response = new Response();
//            $response = $response->withStatus(401, "Unauthorized");
//            $body = require __DIR__ . "/../../views/errors/401.view.php";
//            $response->getBody()->write("$body");
//
//            return $response;
//        }

        return $handler->handle($request);
    }
}