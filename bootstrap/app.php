<?php

use DI\Container;
use Dotenv\Dotenv;
use Jaxon\Config\Config;
use Jaxon\Exception\RequestException;
use Lagdo\Facades\ContainerWrapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy as Group;
use SlimSession\Helper as SessionHelper;

$baseDir = dirname(__DIR__);
require "$baseDir/vendor/autoload.php";

Dotenv::createImmutable($baseDir)->safeLoad();
Dotenv::createImmutable($baseDir, '.env.dbadmin')->safeLoad();

$bootstrapDir = __DIR__;
/** @var Container */
$container = require "$bootstrapDir/container.php";
// Set the container for the facades.
ContainerWrapper::setContainer($container);
// Create the app with the container.
AppFactory::setContainer($container);
$app = AppFactory::create();
// Save the app in the container.
$container->set(App::class, $app);

// Register the DbAdmin package.
$bootDbAdmin = require "$bootstrapDir/dbadmin.php";
[
    $adminConfigMiddleware,
    $auditConfigMiddleware,
    $auditGateMiddleware,
] = $bootDbAdmin($app);

// Middlewares for authentication.
$bootAuth = require "$bootstrapDir/auth.php";
[
    $authStartMiddleware,
    $authCheckMiddleware,
] = $bootAuth($app);

// Jaxon middleware to show error on Ajax requests.
$jaxonErrorMiddleware = require "$bootstrapDir/jaxon.php";

$app->get('/login[/]', function(Request $request, Response $response): Response {
    $session = $this->get(SessionHelper::class);
    $response->getBody()->write('' . jaxon()->view()->render('tpl::auth/login', [
        'errors' => $session->get('errors'),
    ]));
    // The errors are flashed.
    $session->set('errors', []);
    return $response;
})->setName('page_login')
    ->add($adminConfigMiddleware)
    ->add($authStartMiddleware);

$router = function(Group $group, string $page) use($jaxonErrorMiddleware) {
    /**
     * Jaxon middleware to process ajax requests
     *
     * @throws RequestException
     */
    $ajaxMiddleware = fn(Request $request, RequestHandler $handler): Response =>
        jaxon()->psr()->ajax()->process($request, $handler);

    // Show the page
    $group->get('[/]', function(Request $request, Response $response) use($page): Response {
        $response->getBody()->write('' . jaxon()->view()->render("tpl::$page"));
        return $response;
    })->setName("{$page}_page");

    // Nothing to do. The Jaxon ajax middleware processes the request.
    $group->post('/jaxon', fn() => true)
        ->setName("{$page}_ajax")
        ->add($ajaxMiddleware)
        ->add($jaxonErrorMiddleware);
};

// Routes for DbAdmin
$app->group('', fn(Group $group) => $router($group, 'dbadmin'))
    ->add($adminConfigMiddleware)
    ->add($authCheckMiddleware)
    ->add($authStartMiddleware);

// Routes for DbAudit
$app->group('/audit', fn(Group $group) => $router($group, 'dbaudit'))
    ->add($auditGateMiddleware)
    ->add($auditConfigMiddleware)
    ->add($authCheckMiddleware)
    ->add($authStartMiddleware);

/**
 * The routing middleware should be added earlier than the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled by the middleware
 */
$app->addRoutingMiddleware();

// Add Error Handling Middleware
/** @var Config */
$config = $container->get(Config::class);
$displayErrorDetails = $config->getOption('errors.displayDetails', true);
$logErrors = $config->getOption('errors.log', true);
$logErrorDetails = $config->getOption('errors.logDetails', true);

$logger = $container->get(LoggerInterface::class);
$app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails, $logger);

return $app;
