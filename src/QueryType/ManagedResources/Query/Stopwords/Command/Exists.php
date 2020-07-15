<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractExists;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Exists extends AbstractExists
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Stopwords::COMMAND_EXISTS;
    }
}
