<?php

namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractDelete;
use Solarium\QueryType\ManagedResources\Query\Synonyms;

class Delete extends AbstractDelete
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Synonyms::COMMAND_DELETE;
    }
}
