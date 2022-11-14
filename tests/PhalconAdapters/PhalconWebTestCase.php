<?php

declare(strict_types=1);

namespace PhalconAdapters;

use Laminas\Diactoros\ServerRequestFactory;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Http\Request as PhalconRequest;
use Phalcon\Http\Response as PhalconResponse;
use Phalcon\Mvc\Application as MvcApplication;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Mvc\Router;
use PhalconAdapters\Http\RequestConverter;
use PhalconAdapters\Http\ResponseConverter;
use Psr\Http\Message\ServerRequestInterface;

class PhalconWebTestCase extends PhalconTestCase
{
    /** @var \Phalcon\Mvc\Application */
    protected $application;

    protected function setUp() : void
    {
        parent::setUp();

        $this->setUpContainer();

        $this->application = new MvcApplication($this->container);
        $this->application->useImplicitView(false);
    }

    protected function setUpContainer() : void
    {
        $container = $this->container;

        $container->set('eventsManager', function () {
            $eventsManager = new EventsManager();
            $eventsManager->attach('dispatch', new RequestConverter());
            $eventsManager->attach('dispatch', new ResponseConverter());
            return $eventsManager;
        });

        $container->set('router', function () use ($container) {
            $router = new Router(false);
            $router->setDI($container);
            $router->setEventsManager($container->getIfExists('eventsManager'));
            $router->setDefaultNamespace('PhalconAdapters\Assets\Controller');
            $router->setDefaultController('index');
            $router->setDefaultAction('index');
            $router->removeExtraSlashes(true);
            $router->notFound([
                'controller' => 'index',
                'action'     => 'route404',
            ]);
            return $router;
        });

        $container->set('dispatcher', function () use($container) {
            $dispatcher = new MvcDispatcher();
            $dispatcher->setDI($container);
            $dispatcher->setEventsManager($container->getIfExists('eventsManager'));
            return $dispatcher;
        });

        $container->set('request', function () {
            return new PhalconRequest();
        });

        $container->set('response', function () {
            return new PhalconResponse();
        });

        $container->set(ServerRequestInterface::class, function() {
            return ServerRequestFactory::fromGlobals();
        });
    }

    protected function runApplication(string $uri) : \Phalcon\Http\ResponseInterface
    {
        $dispatcher = $this->container->get('dispatcher');
        \assert($dispatcher instanceof Dispatcher);
        $router = $this->container->get('router');
        \assert($router instanceof Router);
        $router->handle($uri);

        $dispatcher->setModuleName($router->getModuleName());
        $dispatcher->setNamespaceName($router->getNamespaceName());
        $dispatcher->setControllerName($router->getControllerName());
        $dispatcher->setActionName($router->getActionName());
        $dispatcher->setParams($router->getParams());
        $dispatcher->dispatch();

        return $dispatcher->getReturnedValue();
    }
}
