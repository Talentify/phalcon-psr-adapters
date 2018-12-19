<?php

declare(strict_types=1);

namespace PhalconAdapters\Http;

use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use PhalconAdapters\Assets\HasDependencyInjectionContainer;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestConverterTest extends TestCase
{
    use HasDependencyInjectionContainer;

    /**
     * @test
     */
    public function shouldAddPhalconRouterParamsToPsrServerRequest() : void
    {
        $params = [
            'param1' => 'bar',
            'param2' => 'baz',
        ];

        $router = $this->iHaveRouterForParams($params);
        $this->iDispatchTheRouter($router);

        $request = $this->getContainer()->get(ServerRequestInterface::class);
        \assert($request instanceof ServerRequestInterface);

        $this->assertEquals($params, $router->getParams());
        $this->assertEquals($params, $request->getAttributes());
    }

    /**
     * @param mixed[] $params
     */
    private function iHaveRouterForParams(array $params) : Router
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
    private function iDispatchTheRouter(Router $router) : void
    {
        $dispatcher = $this->getContainer()->get('dispatcher');
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
