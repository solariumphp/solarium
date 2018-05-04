<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSet;

/**
 * Facet query.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/json-facet-api.html
 */
class JsonQuery extends AbstractQuery
{
    use JsonFacetTrait;

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType()
    {
        return FacetSet::FACET_JSON_QUERY;
    }
}
