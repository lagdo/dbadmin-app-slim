<?php

use Jaxon\App\I18n\Translator;
use Jaxon\Exception\Exception as JaxonException;
use Lagdo\DbAdmin\App\Ajax\Exception\AppException;
use Lagdo\DbAdmin\App\Ajax\Exception\ValidationException;
use Lagdo\DbAdmin\Driver\Exception\DriverException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

return function(Request $request, RequestHandler $handler): Response {
    $jaxon = jaxon();
    $trans = $jaxon->di()->g(Translator::class);

    $dialog = $jaxon->getResponse()->dialog();
    $warningHandler = fn(Exception $e) =>
        $dialog->title($trans->trans('Warning'))->warning($e->getMessage());
    $errorHandler = fn(Exception $e) =>
        $dialog->title($trans->trans('Error'))->error($e->getMessage());
    $jaxon->callback()
        ->error($warningHandler, AppException::class)
        ->error($warningHandler, ValidationException::class)
        ->error($errorHandler, DriverException::class)
        ->error($errorHandler, JaxonException::class)
        ->error($errorHandler);

    // Load the Jaxon global functions.
    $jaxon->setAppOption('helpers.global', true);

    // Proceed with the next middleware
    return $handler->handle($request);
};
