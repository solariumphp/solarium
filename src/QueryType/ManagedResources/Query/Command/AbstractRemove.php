<?php

namespace Solarium\QueryType\ManagedResources\Query\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;

abstract class AbstractRemove extends AbstractCommand
{
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
