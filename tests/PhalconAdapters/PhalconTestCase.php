<?php

declare(strict_types=1);

namespace PhalconAdapters;

use Phalcon\Di\Di;
use PHPUnit\Framework\TestCase;

class PhalconTestCase extends TestCase
{
    /** @var \Phalcon\Di\FactoryDefault */
    protected $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
        Di::setDefault($this->container);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->container::reset();
    }
}
