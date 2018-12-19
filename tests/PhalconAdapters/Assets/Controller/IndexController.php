<?php

declare(strict_types=1);

namespace PhalconAdapters\Assets\Controller;

use Phalcon\Http\ResponseInterface as PhalconResponseInterface;
use Phalcon\Mvc\Controller;
use PhalconAdapters\Http\ResponseAdapter;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class IndexController extends Controller
{
    public function indexAction() : PhalconResponseInterface
    {
        $request = ServerRequestFactory::fromGlobals();

        $response = new Response();

        $response->getBody()->write('Hello World!');

        return new ResponseAdapter($response);
    }
}
