<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;

/**
 * Command Add base class.
 */
abstract class AbstractAdd extends AbstractCommand
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Query::COMMAND_ADD;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        return Request::METHOD_PUT;
    }

    /**
     * Returns the data to be sent to Solr.
     *
     * @return string|null
     */
    abstract public function getRawData(): ?string;
}
