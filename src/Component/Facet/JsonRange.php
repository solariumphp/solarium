<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet range.
 *
 * @see https://solr.apache.org/guide/json-facet-api.html#range-facet
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
