<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

include_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Cli\Console as CliApplication;
use Phalcon\Cli\Dispatcher as CliDispatcher;

$container = new FactoryDefault();
$container->set('dispatcher', function () {
    $dispatcher = new MvcDispatcher();
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
