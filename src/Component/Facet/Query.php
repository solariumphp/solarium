<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Query\Helper;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#facet.query_:_Arbitrary_Query_Faceting
 */
class Query extends AbstractFacet implements QueryInterface, ExcludeTagsInterface
{
    use ExcludeTagsTrait;
    use QueryTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'query' => '*:*',
    ];

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_QUERY;
    }

    /**
     * Returns a query helper.
     *
     * @return \Solarium\Core\Query\Helper
     */
    public function getHelper(): Helper
    {
        return new Helper();
    }
}
