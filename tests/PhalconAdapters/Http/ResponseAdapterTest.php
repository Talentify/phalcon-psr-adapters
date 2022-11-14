<?php

declare(strict_types=1);

namespace PhalconAdapters\Http;

use Laminas\Diactoros\Response;
use Phalcon\Support\Version;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseAdapterTest extends TestCase
{
    /**
     * @test
     */
    public function shouldConvertTheBodyToString() : void
    {
        $adapter = new ResponseAdapter($this->iHaveCommonPsrResponse());

        $this->assertEquals('Hello World!', $adapter->getContent());
    }

    public function testGetHeaders() : void
    {
        $adapter = new ResponseAdapter($this->iHaveCommonPsrResponse());

        $this->assertEquals(
            [
                'HTTP/1.1 200 OK' => null,
                'Status' => '200 OK',
            ],
            $adapter->getHeaders()->toArray()
        );

    }

    /**
     * @test
     */
    public function shouldGetTheStatusCode() : void
    {
        $adapter = new ResponseAdapter($this->iHaveCommonPsrResponse());

        $this->assertEquals(200, $adapter->getStatusCode());
    }

    /**
     * @test
     */
    public function shouldGetTheReasonPhrase() : void
    {
        if ($this->getPhalconVersion() <= 33) {
            $this->markTestSkipped('Method getReasonPhrase() does not exists until Phalcon 3.4.');
        }

        $adapter = new ResponseAdapter($this->iHaveCommonPsrResponse());

        $this->assertEquals('OK', $adapter->getReasonPhrase());
    }

    private function iHaveCommonPsrResponse() : ResponseInterface
    {
        $response = new Response();

        $response->getBody()->write('Hello World!');

        return $response;
    }

    private function getPhalconVersion() : int
    {
        return (int)(
            (new Version())->getPart(Version::VERSION_MAJOR) .
            (new Version())->getPart(Version::VERSION_MEDIUM)
        );
    }
}
