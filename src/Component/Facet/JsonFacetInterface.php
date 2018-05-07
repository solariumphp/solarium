<?php

namespace Solarium\Component\Facet;

/**
 * Json facets.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/json-facet-api.html
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
