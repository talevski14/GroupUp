<?php

namespace Controllers;

use DI\Container;
use Knlv\Slim\Views\TwigMessages;
use services\SocietyService;
use services\UserService;
use Slim\Flash\Messages;

class Controller
{
    protected Container $container;
    protected UserService $userService;
    protected SocietyService $societyService;
    public function __construct($container)
    {
        $this->container = $container;
        $this->userService = $this->container->get(UserService::class);
        $this->societyService = $this->container->get(SocietyService::class);
    }
}