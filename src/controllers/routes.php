<?php

use Controllers\DashboardController;
use Controllers\EditController;
use Controllers\EventController;
use Controllers\HomeController;
use Middleware\AuthorizationMiddleware;
use Middleware\EventMiddleware;
use Middleware\GuestMiddleware;
use Middleware\MemberMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->get('/', [HomeController::class, "index"])->add(new GuestMiddleware());
$app->post('/login', [HomeController::class, "login"])->add(new GuestMiddleware());
$app->get('/signup', [HomeController::class, "signup"])->add(new GuestMiddleware());
$app->post('/signup', [HomeController::class, "create"])->add(new GuestMiddleware());
$app->get('/photo', [HomeController::class, "add_photo"])->add(new MemberMiddleware());
$app->post('/photo', [HomeController::class, "upload"])->add(new MemberMiddleware());
$app->get('/logout', [HomeController::class, "logout"])->add(new MemberMiddleware());

$app->get('/invite-link', [DashboardController::class, "accept"])->add(new MemberMiddleware());

$app->get('/home', [DashboardController::class, "dashboard"])->add(new MemberMiddleware());

$app->group('/account', function (RouteCollectorProxy $group) {
    $group->group('/edit', function (RouteCollectorProxy $group) {
        $group->get('', [EditController::class, "edit"]);
        $group->post('', [EditController::class, "editAccount"]);
        $group->get('/password', [EditController::class, "editPassword"]);
        $group->post('/password', [EditController::class, "changePassword"]);
    })->add(new MemberMiddleware());
    $group->get('/remove', [EditController::class, "remove"])->add(new MemberMiddleware());
    $group->post('/activate', [EditController::class, "activate"])->add(new GuestMiddleware());
});

$app->group('/society', function (RouteCollectorProxy $group) use ($container){
    $group->get('/create', [DashboardController::class, "create"]);
    $group->post('/create', [DashboardController::class, "insert"]);
    $group->group('/{id}', function (RouteCollectorProxy $group) use ($container){
        $group->group('/event', function (RouteCollectorProxy $group) use ($container){
            $group->get('/create', [EventController::class, "create"]);
            $group->post('/create', [EventController::class, "upload"]);
            $group->post('/comment', [EventController::class, "comment"]);
            $group->post('/response', [EventController::class, "response"]);
            $group->group('/{idEvent}', function (RouteCollectorProxy $group) use ($container) {
                $group->get('/edit', [EventController::class, "edit"])->add(new EventMiddleware($container));
                $group->post('/edit', [EventController::class, "update"])->add(new EventMiddleware($container));
                $group->post('/delete', [EventController::class, "delete"])->add(new EventMiddleware($container));
            });
        });
        $group->get('/leave', [DashboardController::class, "leave"]);
        $group->get('/invite-link', [DashboardController::class, "generate"]);
        $group->get('', [EventController::class, "index"]);
    })->add(new AuthorizationMiddleware($container));
})->add(new MemberMiddleware());