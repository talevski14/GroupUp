<?php

use Controllers\DashboardController;
use Controllers\EditController;
use Controllers\EventController;
use Controllers\HomeController;
use Core\Database;
use Core\Weather;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;

$container->set('flash', function() {
    $storage = [];
    return new Messages($storage);
});

$container->set('view', function() {
    return Twig::create(__DIR__ . '/../views/', ['cache' => false]);
});

$container->set('config', function() {
    return require __DIR__ . "/../src/config.php";
});

$container->set('db', function() use ($container) {
    $config = $container->get("config")['database'];
    return new Database(new PDO("mysql:host=" . $config["host"] . ";port=" . $config["port"] . ";dbname=" . $config["dbname"] . ";charset=" . $config["charset"] . ";", "" . $config["username"], "" . $config["password"]));
});

$container->set(HomeController::class, function (ContainerInterface $container) {
    return new HomeController($container);
});

$container->set(DashboardController::class, function (ContainerInterface $container) {
    return new DashboardController($container);
});

$container->set(EditController::class, function (ContainerInterface $container)
{
    return new EditController($container);
});

$container->set(EventController::class, function (ContainerInterface $container)
{
    return new EventController($container);
});

$container->set('client', function() {
    return new Client();
});

$container->set('weather', function() use ($container) {
    return new Weather($container->get("client"), $container->get("config"));
});

$container->set('settings', function () {
    return [
        'doctrine' => require __DIR__ . '/../config/doctrine.php'
    ];
});

$container->set('EntityManager', function () use ($container) {
    return $container->get('settings')['doctrine'];
});
