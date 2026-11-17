<?php

use App\Facade\Session;
use App\Response\ResponseStream;
use DI\Container;
use Dotenv\Dotenv;
use Jaxon\Config\Config;
use Jaxon\Exception\RequestException;
use Lagdo\DbAdmin\Support\Facade\Export;
use Lagdo\DbAdmin\Support\Facade\Import;
use Lagdo\DbAdmin\Support\Service\Export\ExportService;
use Lagdo\DbAdmin\Support\Service\Import\ImportService;
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
    $loginConfigMiddleware,
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
    /** @var SessionHelper */
    $session = $this->get(SessionHelper::class);
    $jaxon = jaxon();
    $response->getBody()->write('' . $jaxon->view()->render('dbadmin::auth/login', [
        'errors' => $session->get('errors'),
        'builtWith' => $jaxon->di()->g('built_with'),
    ]));
    // The errors are flashed.
    $session->set('errors', []);
    return $response;
})->setName('page_login')
    ->add($loginConfigMiddleware)
    ->add($authStartMiddleware);

$router = function(Group $group, string $page) use($jaxonErrorMiddleware) {
    /**
     * @throws RequestException
     */
    $ajaxMiddleware = fn(Request $request, RequestHandler $handler): Response =>
        jaxon()->psr()->ajax()->process($request, $handler);

    // Show the page
    $group->get('[/]', function(Request $request, Response $response) use($page): Response {
        $response->getBody()->write('' . jaxon()->view()->render("dbadmin::$page"));
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

$app->get('/export/{filename}', function(Request $_, Response $response, array $args) {
    $filename = $args['filename'];

    /** @var ExportService */
    $service = Export::instance();
    $options = $service->options($filename, ['timeLimit' => 0]);
    $exports = $options === null ? null : $service->export($options);
    if ($exports === null) {
        $response->getBody()->write("Unable to export file $filename.");
        return $response->withStatus(403)
            ->withHeader('Content-Type', 'text/plain');
    }

    // Unlock the session before starting the streaming, so the next requests can be processed.
    session_write_close();

    $stream = new ResponseStream($exports);
    return $response->withBody($stream)
        ->withHeader('Content-Type', $options->contentType())
        ->withHeader('Content-Disposition', $options->contentDisposition())
        ->withHeader('Cache-Control', 'no-cache')
        ->withHeader('Connection', 'keep-alive')
        ->withHeader('X-Accel-Buffering', 'no');
})->setName('dbadmin_export')
    ->add($adminConfigMiddleware)
    ->add($authCheckMiddleware)
    ->add($authStartMiddleware);

$app->get('/import/{filename}', function(Request $_, Response $response, array $args) {
    $filename = $args['filename'];

    /** @var ImportService */
    $service = Import::instance();
    $options = $service->options($filename, ['timeLimit' => 0]);
    $imports = $options === null ? null : $service->import($options);
    if ($imports === null) {
        $response->getBody()->write("Unable to import file $filename.");
        return $response->withStatus(403)
            ->withHeader('Content-Type', 'text/plain');
    }

    // Unlock the session before starting the streaming, so the next requests can be processed.
    session_write_close();

    $stream = new ResponseStream($imports);
    return $response->withBody($stream)
        ->withHeader('Content-Type', $options->contentType())
        ->withHeader('Content-Disposition', $options->contentDisposition())
        ->withHeader('Cache-Control', 'no-cache')
        ->withHeader('Connection', 'keep-alive')
        ->withHeader('X-Accel-Buffering', 'no');
})->setName('dbadmin_import')
    ->add($adminConfigMiddleware)
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
