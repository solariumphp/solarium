<?php

namespace Solarium\QueryType\Server\Query\Action;

interface AsyncActionInterface extends ActionInterface
{
    /**
     * Can be used to set a requestId to track this action with asynchronous processing.
     *
     * @param string $async
     *
     * @return self Provides fluent interface
     */
    public function setAsync(string $async): self;

    /**
     * Get the request-id for asynchronous processing.
     *
     * @return string
     */
    public function getAsync(): string;
}
