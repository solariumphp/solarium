<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

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
        return FacetSetInterface::FACET_FIELD;
    }

    /**
     * Limit the terms for faceting by a string they must contain. Since Solr 5.1.
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
     * @return string
     */
    public function getContains()
    {
        return $this->getOption('contains');
    }

    /**
     * Case sensitivity of matching string that facet terms must contain. Since Solr 5.1.
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
     * @return bool
     */
    public function getContainsIgnoreCase()
    {
        return $this->getOption('containsignorecase');
    }
}
