<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractDelete;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Delete extends AbstractDelete
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Stopwords::COMMAND_DELETE;
    }
}
