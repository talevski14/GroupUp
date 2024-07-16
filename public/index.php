<?php

use App\Error\Renderer\HtmlErrorRenderer;
use DI\Container;
use Middleware\AuthorizationMiddleware;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$container = new Container();
AppFactory::setContainer($container);

require __DIR__ . "/../src/dependencies.php";

date_default_timezone_set("Europe/Skopje");

$auth = new AuthorizationMiddleware($container);

$app = AppFactory::create();
$app->add(
    function ($request, $next) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->get('flash')->__construct($_SESSION);

        return $next->handle($request);
    }
);

$app->add(TwigMiddleware::createFromContainer($app));

$displayErrorDetails = (bool)($_ENV['DEBUG'] ?? false);
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
//$errorHandler->registerErrorRenderer('text/html', HtmlErrorRenderer::class);

require __DIR__ . "/../src/controllers/routes.php";

$app->run();