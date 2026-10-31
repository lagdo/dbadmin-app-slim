<?php

use Lagdo\DbAdmin\App\DbAdminPackage;
use Lagdo\DbAdmin\App\DbAuditPackage;
use Lagdo\DbAdmin\App\Ui\AdminUiBuilder;
use Lagdo\DbAdmin\App\Ui\AuditUiBuilder;
use Lagdo\DbAdmin\Support\Facade\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

use function Jaxon\jaxon;

return function(App $app) {
    // Middleware to register the DbAdmin services.
    $loginConfig = function(Request $request, RequestHandler $handler): Response {
        $baseDir = dirname(__DIR__);
        DbAdminPackage::register("$baseDir/config/dbadmin", '/jaxon');

        $jaxon = jaxon();
        $template = $jaxon->getAppOption('template', '');
        $jaxon->view()
            ->addNamespace('dbadmin', "$baseDir/templates/$template", '.php', 'jaxon');

        return $handler->handle($request);
    };

    // Middleware to register the DbAdmin package.
    $adminConfig = function(Request $request, RequestHandler $handler): Response {
        $baseDir = dirname(__DIR__);
        DbAdminPackage::register("$baseDir/config/dbadmin", '/jaxon');

        $jaxon = jaxon();
        $template = $jaxon->getAppOption('template', '');
        $view = $jaxon->view();
        $view->addNamespace('dbadmin', "$baseDir/templates/$template", '.php', 'jaxon');
        $view->shareValues([
            'jaxon' => $jaxon,
            'di' => $jaxon->di(),
            'ui' => AdminUiBuilder::class,
            'package' => DbAdminPackage::class,
        ]);

        // Call the Jaxon Psr7 config middleware.
        return $jaxon->psr()
            ->config("$baseDir/config/jaxon.php")
            ->process($request, $handler);
    };

    // Middleware to register the DbAudit package.
    $auditConfig = function(Request $request, RequestHandler $handler): Response {
        $baseDir = dirname(__DIR__);
        DbAuditPackage::register("$baseDir/config/dbadmin", '/audit/jaxon');

        $jaxon = jaxon();
        $template = $jaxon->getAppOption('template', '');
        $view = $jaxon->view();
        $view->addNamespace('dbadmin', "$baseDir/templates/$template", '.php', 'jaxon');
        $view->shareValues([
            'jaxon' => $jaxon,
            'di' => $jaxon->di(),
            'ui' => AuditUiBuilder::class,
            'package' => DbAuditPackage::class,
        ]);

        // Call the Jaxon Psr7 config middleware.
        return $jaxon->psr()
            ->config("$baseDir/config/jaxon.php")
            ->process($request, $handler);
    };

    $auditGate = function(Request $request, RequestHandler $handler) use($app): Response {
        $jaxon = jaxon();
        if ($jaxon->di()->g(DbAuditPackage::class)->checkAuditAccess(Auth::userId())) {
            // Proceed with the next middleware
            return $handler->handle($request);
        }

        // Forbidden. Return an error.
        if ($jaxon->canProcessRequest()) {
            $response = $jaxon->ajaxResponse();
            $response->alert('Forbidden');
            return $response->toPsr();
        }

        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write((string)$jaxon->view()->render('dbadmin::auth/403'));
        return $response->withStatus(403);
    };

    return [$loginConfig, $adminConfig, $auditConfig, $auditGate];
};
