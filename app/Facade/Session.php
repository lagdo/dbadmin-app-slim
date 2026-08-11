<?php

namespace App\Facade;

use Lagdo\Facades\AbstractFacade;
use Lagdo\Facades\ServiceInstance;
use SlimSession\Helper as SessionHelper;

/**
 * @extends AbstractFacade<SessionHelper>
 * @method static mixed get(string $key, mixed $default = null)
 * @method static self set(string $key, mixed $value)
 */
class Session extends AbstractFacade
{
    use ServiceInstance;

    /**
     * @inheritDoc
     */
    protected static function getServiceIdentifier(): string
    {
        return SessionHelper::class;
    }
}
