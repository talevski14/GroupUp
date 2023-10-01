<?php

namespace Controllers;

use DI\Container;
use Knlv\Slim\Views\TwigMessages;
use Slim\Flash\Messages;

class Controller
{
    protected Container $container;
    public function __construct($container)
    {
        $this->container = $container;
    }
}