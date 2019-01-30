<?php

namespace Solarium\QueryType\Server\CoreAdmin\Query\Action;

use Solarium\Core\Configurable;

/**
 * CoreAdmin query command base class.
 */
abstract class AbstractAction extends Configurable
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    abstract public function getType(): string;
}
