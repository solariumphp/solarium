<?php

namespace Solarium\QueryType\Select\Result;

/**
 * Solr result document interface.
 */
interface DocumentInterface
{
    /**
     * Constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields);
}
