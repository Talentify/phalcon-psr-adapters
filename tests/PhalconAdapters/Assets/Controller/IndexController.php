<?php

declare(strict_types=1);

namespace PhalconAdapters\Assets\Controller;

use Laminas\Diactoros\Response;

class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction() : Response
    {
        $response = new Response();

        $response->getBody()->write('Hello from the default controller.');

        return $response;
    }

    public function route404Action() : Response
    {
        $response = new Response();

        $response->getBody()->write('You route was not found by phalcon.');

        return $response;
    }
}
