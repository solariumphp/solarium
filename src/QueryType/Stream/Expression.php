<?php

namespace Solarium\QueryType\Stream;

use Solarium\Exception\InvalidArgumentException;

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
     *
     * @throws InvalidArgumentException
     */
    public function __call(string $name, array $arguments)
    {
        return $name.'('.implode(', ', array_filter($arguments, function ($value) {
            if (is_array($value) || (is_object($value) && !method_exists($value, '__toString'))) {
                throw new InvalidArgumentException('An expression argument must be a scalar value or an object that provides a __toString() method.');
            }
            if (is_string($value)) {
                $value = trim($value);
            }
            // Eliminate empty string arguments.
            return '' !== $value;
        })).')';
    }
}
