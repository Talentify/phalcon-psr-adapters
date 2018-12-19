# Phalcon adapters for PSRs related to HTTP messages

## PSR-7: HTTP message interfaces

### Request
[PSR-7](https://www.php-fig.org/psr/psr-7/) defines the following interfaces that are related
to the HTTP request:
* `Psr\Http\Message\MessageInterface`
* `Psr\Http\Message\RequestInterface`
* `Psr\Http\Message\ServerRequestInterface`
* `Psr\Http\Message\UploadedFileInterface`
* `Psr\Http\Message\UriInterface`

In order to an Adapter to be useful it must do:
* Convert `Phalcon\Http\Request` to `Psr\Http\Message\ServerRequestInterface`
* Convert `Psr\Http\Message\ServerRequestInterface` to `Phalcon\Http\Request`

Due to the way Phalcon's request was built it is difficult to create an adapter because:
* There is no easy way to override request data
* Some methods do too much and/or has private access
* It needs a whole new implementation of `Psr\Http\Message\UriInterface`

Because of that we currently opted by using a
[PSR compliant lib](https://packagist.org/providers/psr/http-message-implementation)
(like [zend diactoros](https://docs.zendframework.com/zend-diactoros/))
instead of using Phalcon's request.

### Response
Currently there is an adapter to convert `Psr\Http\Message\ResponseInterface` to `Phalcon\Http\Response`.
The easiest way to use it is registering a listener on your events manager, like this:
```php
<?php

use PhalconAdapters\Http\ResponseDispatcher;

$container = new \Phalcon\Di\FactoryDefault();
$container->set('eventsManager', function() {
    $eventsManager = new \Phalcon\Events\Manager();
    $eventsManager->attach('dispatch', new ResponseDispatcher());
    return $eventsManager;
});
$container->set('dispatcher', function () use ($container) {
    $dispatcher = new \Phalcon\Mvc\Dispatcher();
    $dispatcher->setEventsManager($container->get('eventsManager'));
    return $dispatcher;
});
```

Now you can just return PSR responses:
```php
<?php

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
```

#### Limitations
* You can not use cookies because PSR-7 has no clear statement related on how to handle it.
* You can not send files because the way Phalcon send files requires some work to be done.
