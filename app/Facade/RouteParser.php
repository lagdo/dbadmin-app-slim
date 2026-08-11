<?php

namespace App\Facade;

use Lagdo\Facades\AbstractFacade;
use Lagdo\Facades\ServiceInstance;
use Slim\Interfaces\RouteParserInterface;

/**
 * @extends AbstractFacade<RouteParserInterface>
 * @method static string urlFor(string $routeName, array $data = [], array $queryParams = [])
 */
class RouteParser extends AbstractFacade
{
    use ServiceInstance;

    /**
     * @inheritDoc
     */
    protected static function getServiceIdentifier(): string
    {
        return RouteParserInterface::class;
    }
}
