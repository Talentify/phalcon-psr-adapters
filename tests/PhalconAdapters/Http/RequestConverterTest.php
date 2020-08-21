<?php

declare(strict_types=1);

namespace PhalconAdapters\Http;

use PhalconAdapters\PhalconWebUtils;
use PhalconAdapters\PhalconWebTestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestConverterTest extends PhalconWebTestCase
{
    use PhalconWebUtils;

    /**
     * @test
     */
    public function shouldAddPhalconRouterParamsToPsrServerRequest() : void
    {
        $params = [
            'param1' => 'bar',
            'param2' => 'baz',
        ];

        $router = $this->iHaveRouterWithParams($params);
        $this->iDispatchTheRouter($router);

        $request = $this->container->get(ServerRequestInterface::class);
        \assert($request instanceof ServerRequestInterface);

        $this->assertEquals($params, $router->getParams());
        $this->assertEquals($params, $request->getAttributes());
    }
}
