<?php

declare(strict_types=1);

namespace PhalconAdapters\Http;

use Phalcon\Http\Response as PhalconResponse;
use Phalcon\Http\Response\Headers as PhalconHeaders;
use Psr\Http\Message\ResponseInterface;

/**
 * This class converts a PSR response to a Phalcon response.
 * Use it as the return of your controllers.
 *
 * It is necessary to this class to implement \Phalcon\Http\ResponseInterface
 * in order to be correctly handled by \Phalcon\Mvc\Application::handle()
 */
class ResponseAdapter extends PhalconResponse
{
    /**
     * @throws \InvalidArgumentException if response body is not a string.
     */
    public function __construct(ResponseInterface $response)
    {
        parent::__construct();

        $this->convertBody($response);
        $this->convertHeader($response);
        // This must be placed after headers conversion because of PhalconResponse::getStatusCode()
        $this->setStatusCode($response->getStatusCode(), $response->getReasonPhrase());
    }

    /**
     * @throws \InvalidArgumentException if response body is not a string.
     */
    protected function convertBody(ResponseInterface $response) : void
    {
        $stream = $response->getBody();
        $uri    = $stream->getMetadata('uri');

        // If stream is a string https://www.php-fig.org/psr/psr-7/#13-streams
        if(\in_array($uri, ['php://memory', 'php://temp'], true)) {
            $this->setContent((string)$stream);
            return;
        }

        throw new \InvalidArgumentException('Only strings are allowed as the response body.');
    }

    /**
     * Note: multiple values for a single header will not work because
     * \Phalcon\Http\Response\Headers set() and send() methods
     * replace the headers.
     */
    protected function convertHeader(ResponseInterface $response) : void
    {
        $headers = new PhalconHeaders();

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers->set($name, $value);
            }
        }

        $this->setHeaders($headers);
    }

    protected function convertCookies(ResponseInterface $response) : void
    {
        // #TODO
//        $this->setCookies();
    }
}
