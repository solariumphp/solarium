<?php

namespace Solarium\QueryType\ManagedResources\Query\Command\Synonyms;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractCreate;

class Create extends AbstractCreate
{
    /**
     * Returns the raw data to be sent to Solr.
     */
    public function getRawData(): string
    {
        return json_encode(['class' => 'org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager']);
    }
}
