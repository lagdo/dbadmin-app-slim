<?php

use DI\ContainerBuilder;
use Jaxon\Config\Config;
use Jaxon\Config\ConfigSetter;
use Lagdo\DbAdmin\Driver\Utils\TranslatorInterface;
use Lagdo\DbAdmin\Support\Provider\AuthInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Interfaces\RouteParserInterface;
use SlimSession\Helper as SessionHelper;

use function Jaxon\jaxon;

$builder = new ContainerBuilder();
$builder->addDefinitions([
    Config::class => function() {
        $configSetter = new ConfigSetter();
        $appOptions = require __DIR__ . '/../config/app.php';
        return $configSetter->newConfig($appOptions);
    },
    LoggerInterface::class => function(ContainerInterface $c) {
        $config = $c->get(Config::class);
        $logger = new Logger($config->getOption('logger.name'));

        $level = $config->getOption('logger.level');
        $path = $config->getOption('logger.path', '');
        if ($path !== '') {
            $logger->pushHandler(new StreamHandler($path, $level));
        }
        if (isset($_ENV['docker'])) {
            $logger->pushHandler(new StreamHandler('php://stdout', $level));
        }

        return $logger;
    },
    // The facades need this to be defined.
    SessionHelper::class => fn() => new SessionHelper(),
    RouteParserInterface::class => fn(ContainerInterface $c) =>
        $c->get(App::class)->getRouteCollector()->getRouteParser(),
    AuthInterface::class => fn() => jaxon()->di()->g(AuthInterface::class),
    TranslatorInterface::class => fn() => jaxon()->di()->g(TranslatorInterface::class),
]);

$builder->useAutowiring(false);
$builder->useAttributes(false);

return $builder->build();
