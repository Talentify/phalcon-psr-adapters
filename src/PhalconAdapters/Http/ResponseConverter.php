<?php

declare(strict_types=1);

namespace PhalconAdapters\Http;

use Phalcon\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Psr\Http\Message\ResponseInterface;

/**
 * This class is responsible for converting the PSR response returned
 * by a controller into a Phalcon's response.
 */
class ResponseConverter
{
    /**
     * This is executed if the event triggered is 'afterDispatchLoop'.
     * @throws \Phalcon\Mvc\Dispatcher\Exception if ResponseAdapter can not handle it.
     */
    public function afterDispatchLoop(Event $event, Dispatcher $dispatcher) : void
    {
        $response = $dispatcher->getReturnedValue();

        if (!$response instanceof ResponseInterface) {
            return;
        }

        try {
            $dispatcher->setReturnedValue(new ResponseAdapter($response));
        } catch (\InvalidArgumentException $e) {
            throw new DispatcherException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
