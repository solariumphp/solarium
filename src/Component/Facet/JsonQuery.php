<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSet;
use Solarium\Core\Query\Helper;

/**
 * Facet query.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/json-facet-api.html
 */
class JsonQuery extends AbstractFacet
{
    use JsonFacetTrait;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'q' => '*:*',
    ];

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType()
    {
        return FacetSet::JSON_FACET_QUERY;
    }

    /**
     * Set the query string.
     *
     * This overwrites the current value
     *
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery($query, $bind = null)
    {
        if (null !== $bind) {
            $helper = new Helper();
            $query = $helper->assemble($query, $bind);
        }

        return $this->setOption('q', $query);
    }

    /**
     * Get the query string.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->getOption('q');
    }
}
