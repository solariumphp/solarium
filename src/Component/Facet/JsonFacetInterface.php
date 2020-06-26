<?php

namespace Solarium\Component\Facet;

/**
 * JSON facets.
 *
 * @see https://lucene.apache.org/solr/guide/json-facet-api.html
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
