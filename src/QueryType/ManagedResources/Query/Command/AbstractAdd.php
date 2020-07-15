<?php

namespace Solarium\QueryType\ManagedResources\Query\Command;

use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;

/**
 * Command Add base class.
 */
abstract class AbstractAdd extends AbstractCommand
{
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
     * Empty.
     *
     * @return string
     */
    public function getTerm(): string
    {
        return '';
    }
}
