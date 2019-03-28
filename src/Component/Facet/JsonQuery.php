<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Query\Helper;

/**
 * Facet query.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/json-facet-api.html
 */
class JsonQuery extends AbstractFacet implements JsonFacetInterface, FacetSetInterface, QueryInterface
{
    use JsonFacetTrait {
        serialize as jsonFacetTraitSerialize;
    }
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
        return FacetSetInterface::JSON_FACET_QUERY;
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

    public function serialize()
    {
        $options = $this->jsonFacetTraitSerialize();
        $options['q'] = $options['query'];
        unset($options['query']);
        return $options;
    }
}
