<?php

namespace Solarium\QueryType\Server\Collections\Query\Action;

use Solarium\Core\ConfigurableInterface;

interface ActionInterface extends ConfigurableInterface
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string;

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

    /**
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string;
}
