<?php

namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractExists;
use Solarium\QueryType\ManagedResources\Query\Synonyms;

class Exists extends AbstractExists
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Synonyms::COMMAND_EXISTS;
    }
}
