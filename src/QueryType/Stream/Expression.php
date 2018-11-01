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

    /**
     * Format and indent a streaming expression.
     *
     * @param string $expression
     *
     * @return string
     */
    public static function indent(string $expression)
    {
        $current_indentation = 0;
        $indentation_step = 2;
        $indented_expression = '';
        for ($c = 0; $c < strlen($expression); ++$c) {
            if ('(' === $expression[$c]) {
                $indented_expression .= $expression[$c].PHP_EOL;
                $current_indentation += $indentation_step;
                $indented_expression .= str_pad('', $current_indentation);
            } elseif (')' === $expression[$c]) {
                $current_indentation -= $indentation_step;
                $indented_expression .= PHP_EOL;
                $indented_expression .= str_pad('', $current_indentation).$expression[$c];
            } elseif (',' === $expression[$c]) {
                $indented_expression .= $expression[$c].PHP_EOL.str_pad('', $current_indentation);
                // swallow space if any
                if (' ' === @$expression[$c + 1]) {
                    ++$c;
                }
            } else {
                $indented_expression .= $expression[$c];
            }
        }
        return $indented_expression;
    }
}
