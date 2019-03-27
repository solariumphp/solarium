<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet range.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Facet_by_Range
 */
class JsonRange extends AbstractRange implements JsonFacetInterface, FacetSetInterface
{
    use JsonFacetTrait {
        init as jsonFacetInit;
    }

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::JSON_FACET_RANGE;
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        parent::init();
        $this->jsonFacetInit();
    }
}
