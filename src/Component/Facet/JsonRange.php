<?php

namespace Solarium\Component\Facet;

/**
 * Facet range.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Facet_by_Range
 */
class JsonRange extends AbstractRange implements JsonFacetInterface
{
    use JsonFacetTrait {
        init as facetSetInit;
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
        $this->facetSetInit();
    }
}
