<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Query\Action;

use Solarium\Core\Configurable;
use Solarium\QueryType\Server\CoreAdmin\Result\Result;

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
    public function getResultClass(): string
    {
        return Result::class;
    }
}
