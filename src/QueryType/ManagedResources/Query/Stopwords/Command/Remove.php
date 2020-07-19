<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

/**
 * Remove.
 */
class Remove extends AbstractCommand
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Stopwords::COMMAND_REMOVE;
    }

    /**
     * Returns request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        return Request::METHOD_DELETE;
    }

    /**
     * Returns the raw data to be sent to Solr.
     *
     * @return string
     */
    public function getRawData(): string
    {
        return '';
    }

    /**
     * Empty.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return '';
    }
}
