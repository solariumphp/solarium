<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

/**
 * JSON facets.
 *
 * @see https://solr.apache.org/guide/json-facet-api.html
 */
interface JsonFacetInterface
{
    /**
     * Serializes nested facets as option "facet" and returns that array structure.
     *
     * @return array|string
     */
    public function serialize();
}
