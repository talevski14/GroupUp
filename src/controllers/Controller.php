<?php

namespace Controllers;

use DI\Container;
use Knlv\Slim\Views\TwigMessages;
use Models\Event;
use Models\Link;
use Services\CommentService;
use Services\EventService;
use Services\implementation\EventServiceImpl;
use Services\implementation\SocietyServiceImpl;
use Services\implementation\UserServiceImpl;
use Services\LinkService;
use Services\SocietyService;
use Services\UserService;
use Slim\Flash\Messages;

class Controller
{
    protected Container $container;
    protected UserService $userService;
    protected SocietyService $societyService;
    protected LinkService $linkService;
    protected EventService $eventService;
    protected CommentService $commentService;
    public function __construct($container)
    {
        $this->container = $container;
        $this->userService = $container->get('userService');
        $this->societyService = $container->get('societyService');
        $this->linkService = $container->get('linkService');
        $this->eventService = $container->get('eventService');
        $this->commentService = $container->get('commentService');
    }
}