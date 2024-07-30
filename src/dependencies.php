<?php

use Controllers\DashboardController;
use Controllers\EditController;
use Controllers\EventController;
use Controllers\HomeController;
use Core\Database;
use Core\Weather;
use GuzzleHttp\Client;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Psr\Container\ContainerInterface;
use Services\caching\CachingDataService;
use Services\caching\DataService;
use Services\implementation\SocietyServiceImpl;
use Services\implementation\UserServiceImpl;
use Services\SocietyService;
use Services\UserService;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Predis\Client as PClient;

$container->set('flash', function() {
    $storage = [];
    return new Messages($storage);
});

$container->set('view', function() {
    return Twig::create(__DIR__ . '/../views/', ['cache' => false]);
});

$container->set('config', function() {
    return require __DIR__ . "/../config/config.php";
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

$container->set('redis', function () {
    return new PClient([
        'scheme' => 'tcp',
        'host' => 'redis',
        'port' => 6379
    ]);
});

$container->set('entityManager', function () use ($container) {
    return $container->get('settings')['doctrine'];
});

$container->set('userService', function ($container) {
    return new Services\implementation\UserServiceImpl($container->get('entityManager'));
});

$container->set('societyService', function ($container) {
    return new Services\implementation\SocietyServiceImpl($container->get('entityManager'));
});

$container->set('linkService', function ($container) {
    return new Services\implementation\LinkServiceImpl($container->get('entityManager'));
});

$container->set('eventService', function ($container) {
    return new Services\implementation\EventServiceImpl($container);
});

$container->set('commentService', function ($container) {
    return new Services\implementation\CommentServiceImpl($container->get('entityManager'), $container->get('redis'));
});

$container->set('rabbitmq', function () {
    return new AMQPStreamConnection('rabbitmq', 5672, 'guest', 'guest');
});

$container->set('notificationService', function ($container) {
    return new Services\implementation\NotificationServiceImpl($container->get('entityManager'));
});

$container->set('dataService', function () {
    return new DataService();
});

$container->set('cachingService', function ($container) {
    return new CachingDataService($container->get('dataService'), $container->get('redis'));
});