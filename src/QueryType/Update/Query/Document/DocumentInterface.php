<?php

namespace Solarium\QueryType\Update\Query\Document;

/**
 * Solr update document interface.
 */
interface DocumentInterface
{
    /**
     * Constructor.
     *
     * @param array $fields
     * @param array $boosts
     * @param array $modifiers
     */
    public function __construct(array $fields = [], array $boosts = [], array $modifiers = []);
}
