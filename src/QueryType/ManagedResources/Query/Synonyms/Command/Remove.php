<?php

namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractRemove;
use Solarium\QueryType\ManagedResources\Query\Synonyms;

class Remove extends AbstractRemove
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Synonyms::COMMAND_REMOVE;
    }
}
