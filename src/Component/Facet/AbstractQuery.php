<?php

namespace Solarium\Component\Facet;

use Solarium\Core\Query\Helper;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#facet.query_:_Arbitrary_Query_Faceting
 */
abstract class AbstractQuery extends AbstractFacet
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'q' => '*:*',
    ];

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
