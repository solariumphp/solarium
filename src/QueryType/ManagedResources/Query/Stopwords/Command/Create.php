<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractCreate;
use Solarium\QueryType\ManagedResources\Query\Stopwords;

class Create extends AbstractCreate
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    public function getType(): string
    {
        return Stopwords::COMMAND_CREATE;
    }

    /**
     * Returns the raw data to be sent to Solr.
     */
    public function getRawData(): string
    {
        return json_encode(['class' => 'org.apache.solr.rest.schema.analysis.ManagedWordSetResource']);
    }
}
