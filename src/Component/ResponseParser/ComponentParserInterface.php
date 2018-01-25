<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Core\Query\AbstractQuery;

/**
 * ComponentParserInterface.
 */
interface ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery $query
     * @param object        $component
     * @param array         $data
     *
     * @return object|null
     */
    public function parse($query, $component, $data);
}
