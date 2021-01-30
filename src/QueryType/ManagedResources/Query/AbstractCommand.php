<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Configurable;

/**
 * Command base class.
 */
abstract class AbstractCommand extends Configurable
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Returns request method.
     *
     * @return string
     */
    abstract public function getRequestMethod(): string;
}
