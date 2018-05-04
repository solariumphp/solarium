<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSet;

/**
 * Facet query.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Field_Value_Faceting_Parameters
 */
class Field extends AbstractField implements ExcludeTagsInterface
{
    use ExcludeTagsTrait;

    /**
     * Facet method enum.
     */
    const METHOD_ENUM = 'enum';

    /**
     * Facet method fc.
     */
    const METHOD_FC = 'fc';

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType()
    {
        return FacetSet::FACET_FIELD;
    }

    /**
     * Limit the terms for faceting by a string they must contain.
     *
     * This is a global value for all facets in this facetset
     *
     * @param string $contains
     *
     * @return self Provides fluent interface
     */
    public function setContains($contains)
    {
        return $this->setOption('contains', $contains);
    }

    /**
     * Get the facet contains.
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getContains()
    {
        return $this->getOption('contains');
    }

    /**
     * Case sensitivity of matching string that facet terms must contain.
     *
     * This is a global value for all facets in this facetset
     *
     * @param bool $containsIgnoreCase
     *
     * @return self Provides fluent interface
     */
    public function setContainsIgnoreCase($containsIgnoreCase)
    {
        return $this->setOption('containsignorecase', $containsIgnoreCase);
    }

    /**
     * Get the case sensitivity of facet contains.
     *
     * This is a global value for all facets in this facetset
     *
     * @return bool
     */
    public function getContainsIgnoreCase()
    {
        return $this->getOption('containsignorecase');
    }
}
