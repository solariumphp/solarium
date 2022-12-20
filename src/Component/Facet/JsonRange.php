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
     * {@internal Both the parent's and JsonFacetTrait's init() are needed
     *            to properly initialize all options.}
     */
    protected function init()
    {
        parent::init();

        $this->jsonFacetInit();
    }
}
