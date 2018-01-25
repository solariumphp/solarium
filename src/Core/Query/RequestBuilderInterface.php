<?php

namespace Solarium\Core\Query;

use Solarium\Core\Client\Request;

/**
 * Interface for requestbuilders.
 */
interface RequestBuilderInterface
{
    /**
     * Build request for a select query.
     *
     * @param QueryInterface $query
     *
     * @return Request
     */
    public function build(QueryInterface $query);
}
