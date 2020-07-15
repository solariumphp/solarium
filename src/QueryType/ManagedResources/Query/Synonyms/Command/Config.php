<?php

namespace Solarium\QueryType\ManagedResources\Query\Synonyms\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractConfig;
use Solarium\QueryType\ManagedResources\Query\Synonyms;

class Config extends AbstractConfig
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Synonyms::COMMAND_CONFIG;
    }
}
