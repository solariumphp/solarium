<?php

namespace Solarium\Core\Query;

/**
 * Solr document interface.
 */
interface DocumentInterface
{
    /**
     * Get all fields.
     *
     * @return array
     */
    public function getFields(): array;
}
