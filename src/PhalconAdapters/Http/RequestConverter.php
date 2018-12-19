<?php

declare(strict_types=1);

namespace PhalconAdapters\Http;

use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class is responsible for adding Phalcon stuff into a PSR server request.
 */
class RequestConverter
{
    /**
     * This is executed if the event triggered is 'beforeDispatchLoop'.
     * @throws \Phalcon\Mvc\Dispatcher\Exception
     */
    public function beforeDispatchLoop(Event $event, Dispatcher $dispatcher) : void
    {
        $container = $dispatcher->getDI();

        $serverRequest = $container->get(ServerRequestInterface::class);
        if (empty($serverRequest)) {
            throw new DispatcherException('There is no server request implementation on DI container.');
        }

        \assert($serverRequest instanceof ServerRequestInterface);
        foreach ($dispatcher->getParams() as $name => $value) {
            $serverRequest = $serverRequest->withAttribute($name, $value);
        }

        $container->set(ServerRequestInterface::class, $serverRequest);
    }
}
