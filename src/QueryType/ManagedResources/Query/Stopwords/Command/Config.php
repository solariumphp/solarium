<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractConfig;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Config extends AbstractConfig
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Stopwords::COMMAND_CONFIG;
    }
}
