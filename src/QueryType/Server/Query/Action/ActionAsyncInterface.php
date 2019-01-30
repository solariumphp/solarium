<?php

namespace Solarium\QueryType\Server\Query\Action;

use Solarium\Core\ConfigurableInterface;

interface ActionAsyncInterface extends ActionInterface
{
    /**
     * Can be used to set a requestId to track this action with asynchronous processing.
     *
     * @param string $async
     *
     * @return self Provides fluent interface
     */
    public function setAsync(string $async);

    /**
     * Get the request-id for asynchronous processing.
     *
     * @return int
     */
    public function getAsync(): int;
}