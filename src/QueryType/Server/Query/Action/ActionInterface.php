<?php

namespace Solarium\QueryType\Server\Query\Action;

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
     * Returns the namespace and class of the result class for the action.
     *
     * @return string
     */
    public function getResultClass(): string;
}
