<?php

declare(strict_types=1);

namespace PhalconAdapters\Assets\Controller;

use Zend\Diactoros\Response;

class IndexController extends \Phalcon\Mvc\Controller
{
    public function indexAction() : Response
    {
        $response = new Response();

        $response->getBody()->write('Hello World!');

        return $response;
    }
}
