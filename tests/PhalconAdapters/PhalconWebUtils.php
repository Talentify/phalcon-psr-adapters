<?php

declare(strict_types=1);

namespace PhalconAdapters;

use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;

trait PhalconWebUtils
{
    /**
     * @param mixed[] $params
     */
    protected function iHaveRouterWithParams(array $params) : Router
    {
        $names  = array_keys($params);
        $values = array_values($params);

        $router  = new Router();
        $pattern = '/someController/someAction/{' . implode('}/{', $names) . '}';
        $router->add($pattern, 'Index::index');

        $uri = '/someController/someAction/' . implode('/', $values);
        $router->handle($uri);

        return $router;
    }

    /**
     * @link https://github.com/phalcon/cphalcon/blob/3.3.x/phalcon/mvc/application.zep#L287
     */
    protected function iDispatchTheRouter(Router $router) : void
    {
        $dispatcher = $this->container->get('dispatcher');
        \assert($dispatcher instanceof Dispatcher);

        $dispatcher->setModuleName($router->getModuleName());
        $dispatcher->setNamespaceName($router->getNamespaceName());
        $dispatcher->setControllerName($router->getControllerName());
        $dispatcher->setActionName($router->getActionName());
        $dispatcher->setParams($router->getParams());

        try {
            $dispatcher->dispatch();
        } catch (\Phalcon\Mvc\Dispatcher\Exception $e) {
            // the controller does not exists and it is OK.
        }
    }
}
