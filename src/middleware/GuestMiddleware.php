<?php

namespace Middleware;

class GuestMiddleware
{
    public function __invoke($request, $handler)
    {
//        if(isset($_SESSION['user']))
//        {
//            header("location: /home");
//        }

        $response = $handler->handle($request);
        return $response;
    }
}