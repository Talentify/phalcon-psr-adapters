<?php

declare(strict_types=1);

namespace PhalconAdapters;

class Container extends \Phalcon\Di\Di
{
    /**
     * Returns the service only if it exists in the container.
     * It is useful in order to not deal with bugs in the services registration.
     *
     * @throws \InvalidArgumentException if service is not in the container
     */
    public function getIfExists($name, $parameters = null)
    {
        if ($this->has($name)) {
            return $this->get($name, $parameters);
        }

        throw new \InvalidArgumentException(sprintf('Service "%s" is not in the DI Container.', $name));
    }
}
