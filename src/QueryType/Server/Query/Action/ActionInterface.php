<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Query\Action;

use Solarium\Core\ConfigurableInterface;

/**
 * ActionInterface.
 */
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
