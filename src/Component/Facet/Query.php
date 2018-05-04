<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSet;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#facet.query_:_Arbitrary_Query_Faceting
 */
class Query extends AbstractQuery
{
    use ExcludeTagsTrait;

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType()
    {
        return FacetSet::FACET_QUERY;
    }
}
