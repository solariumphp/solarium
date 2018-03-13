<?php

namespace Solarium\QueryType\Stream;

/**
 * Stream expression builder.
 **/
class Expression
{
    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return string
     */
    public function __call(string $name, array $arguments)
    {
        return $name.'('.implode(', ', $arguments).')';
    }
}
