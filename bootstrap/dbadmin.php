<?php

use Lagdo\DbAdmin\App\DbAdminPackage;
use Lagdo\DbAdmin\App\DbAuditPackage;
use Lagdo\DbAdmin\Support\Facade\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

return function(App $app) {
    // Middleware to register the DbAdmin package.
    $adminConfig = function(Request $request, RequestHandler $handler): Response {
        $baseDir = dirname(__DIR__);
        $configDir = "$baseDir/config/dbadmin";
        DbAdminPackage::register($configDir, '/jaxon');

        // Call the Jaxon Psr7 config middleware.
        return jaxon()->psr()
            ->config("$baseDir/config/jaxon.php")
            ->process($request, $handler);
    };

    // Middleware to register the DbAudit package.
    $auditConfig = function(Request $request, RequestHandler $handler): Response {
        $baseDir = dirname(__DIR__);
        $configDir = "$baseDir/config/dbadmin";
        DbAuditPackage::register($configDir, '/audit/jaxon');

        // Call the Jaxon Psr7 config middleware.
        return jaxon()->psr()
            ->config("$baseDir/config/jaxon.php")
            ->process($request, $handler);
    };

    $auditGate = function(Request $request, RequestHandler $handler) use($app): Response {
        $jaxon = jaxon();
        if ($jaxon->di()->g(DbAuditPackage::class)->checkAccess(Auth::userId())) {
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
        $response->getBody()->write('' . $jaxon->view()->render('tpl::auth/403'));
        return $response->withStatus(403);
    };

    return [$adminConfig, $auditConfig, $auditGate];
};
