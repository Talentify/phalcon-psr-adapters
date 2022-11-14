<?php

declare(strict_types=1);

namespace PhalconAdapters\Http;

use PhalconAdapters\PhalconWebTestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseConverterTest extends PhalconWebTestCase
{
    /**
     * @test
     *
     * This test works because the 404 route is handled by the IndexController.
     * @see \PhalconAdapters\Assets\Controller\IndexController::route404Action()
     */
    public function shouldTransformPsrResponseToPhalconResponse(): void
    {
        $phalconResponse = $this->runApplication('');

        $this->assertInstanceOf(ResponseAdapter::class, $phalconResponse);
        $this->assertEquals('You route was not found by phalcon.', $phalconResponse->getContent());

        $psrResponse = $this->container->get(ResponseInterface::class);
        $this->assertInstanceOf(ResponseInterface::class, $psrResponse);
        $this->assertEquals('You route was not found by phalcon.', (string)$psrResponse->getBody());
    }
}
