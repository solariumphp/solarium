<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Command\Stopwords;

use Solarium\QueryType\ManagedResources\Query\Command\AbstractCreate;

/**
 * Ceate.
 */
class Create extends AbstractCreate
{
    /**
     * Returns the raw data to be sent to Solr.
     *
     * @return string
     */
    public function getRawData(): string
    {
        return json_encode(['class' => 'org.apache.solr.rest.schema.analysis.ManagedWordSetResource']);
    }
}
