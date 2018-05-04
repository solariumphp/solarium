<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Json facets.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/json-facet-api.html
 */
interface JsonFacetInterface extends FacetSetInterface
{
    /**
     * Serializes nested facets as option "facet" and returns that array structure.
     *
     * @return array
     */
    public function serialize();
}
