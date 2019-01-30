<?php

namespace Solarium\QueryType\Update\Query\Command;

use Solarium\Core\Configurable;

/**
 * Update query command base class.
 */
abstract class AbstractCommand extends Configurable
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    abstract public function getType(): string;
}
