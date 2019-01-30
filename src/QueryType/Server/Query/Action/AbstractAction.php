<?php

namespace Solarium\QueryType\Server\Query\Action;

use Solarium\Core\Configurable;

/**
 * CoreAdmin query command base class.
 */
abstract class AbstractAction extends Configurable implements ActionInterface
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Returns the result class.
     *
     * @return string
     */
    abstract public function getResultClass(): string;
}
