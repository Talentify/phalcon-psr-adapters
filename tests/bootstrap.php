<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

include_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Cli\Console as CliApplication;
use Phalcon\Cli\Dispatcher as CliDispatcher;
use PhalconAdapters\Http\RequestConverter;
use PhalconAdapters\Http\ResponseConverter;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;

$container = new FactoryDefault();
$container->set(ServerRequestInterface::class, function() {
    return ServerRequestFactory::fromGlobals();
});

$container->set('eventsManager', function() {
    $eventsManager = new EventsManager();
    $eventsManager->attach('dispatch', new RequestConverter());
    $eventsManager->attach('dispatch', new ResponseConverter());
    return $eventsManager;
});
$container->set('dispatcher', function () use ($container) {
    $dispatcher = new MvcDispatcher();
    $dispatcher->setEventsManager($container->get('eventsManager'));
    $dispatcher->setDefaultNamespace('PhalconAdapters\Assets\Controller');
    return $dispatcher;
});

$_SERVER = include __DIR__ . '/PhalconAdapters/Assets/ServerVars.php';

$application = new MvcApplication($container);
$application->useImplicitView(false);

$response = $application->handle();
if (PHP_SAPI !== 'cli') {
    $response->send();
}
