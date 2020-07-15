<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractRemove;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Remove extends AbstractRemove
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
}
