<?php

declare(strict_types=1);

namespace PhalconAdapters\Assets;

use Phalcon\DiInterface;

trait HasDependencyInjectionContainer
{
    public function getContainer() : DiInterface
    {
        return \Phalcon\Di::getDefault();
    }
}
