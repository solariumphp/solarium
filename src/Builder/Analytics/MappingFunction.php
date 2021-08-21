<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Builder\Analytics;

use Solarium\Builder\AbstractExpressionVisitor;
use Solarium\Builder\ExpressionInterface;
use Solarium\Builder\FunctionInterface;

/**
 * Mapping Function.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class MappingFunction implements FunctionInterface, ExpressionInterface
{
    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#negation
     */
    public const NEGATION = 'neg';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#absolute-value
     */
    public const ABSOLUTE_VALUE = 'abs';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#analytics-round
     */
    public const ROUND = 'round';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#ceiling
     */
    public const CEILING = 'ceil';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#analytics-floor
     */
    public const FLOOR = 'floor';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#addition
     */
    public const ADDITION = 'add';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#subtraction
     */
    public const SUBTRACTION = 'sub';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#multiplication
     */
    public const MULTIPLICATION = 'mult';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#division
     */
    public const DIVISION = 'div';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#power
     */
    public const POWER = 'pow';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#logarithm
     */
    public const LOGARITHM = 'log';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#analytics-and
     */
    public const AND = 'and';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#analytics-or
     */
    public const OR = 'or';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#exists
     */
    public const EXISTS = 'exists';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#equality
     */
    public const EQUAL = 'equal';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#greater-than
     */
    public const GREATER_THAN = 'gt';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#greater-than-or-equals
     */
    public const GREATER_THAN_EQUALS = 'gte';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#less-than
     */
    public const LESS_THAN = 'lt';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#less-than-or-equals
     */
    public const LESS_THAN_EQUALS = 'lte';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#analytics-top
     */
    public const TOP = 'top';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#bottom
     */
    public const BOTTOM = 'bottom';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#analytics-if
     */
    public const IF = 'if';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#replace
     */
    public const REPLACE = 'replace';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#fill-missing
     */
    public const FILL_MISSING = 'fill_missing';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#remove
     */
    public const REMOVE = 'remove';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#filter
     */
    public const FILTER = 'filter';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#date-parse
     */
    public const DATE_PARSE = 'date';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#analytics-date-math
     */
    public const DATE_MATH = 'date_math';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#explicit-casting
     */
    public const STRING = 'string';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#concatenation
     */
    public const CONCAT = 'concat';

    /**
     * @see https://solr.apache.org/guide/analytics-mapping-functions.html#separated-concatenation
     */
    public const CONCAT_SEPARATED = 'concat_sep';

    /**
     * @var string
     */
    private $type;

    /**
     * @var \Solarium\Builder\FunctionInterface[]|float[]|string[]
     */
    private $arguments;

    /**
     * @param string                                                 $type
     * @param \Solarium\Builder\FunctionInterface[]|float[]|string[] $arguments
     */
    public function __construct(string $type, array $arguments)
    {
        $this->type = $type;

        foreach ($arguments as $argument) {
            $this->arguments[] = \is_array($argument) ? sprintf('[%s]', implode(',', $argument)) : $argument;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return sprintf('%s(%s)', $this->type, implode(',', array_map('strval', $this->arguments)));
    }

    /**
     * {@inheritdoc}
     */
    public function visit(AbstractExpressionVisitor $visitor)
    {
        return $visitor->walkExpression($this);
    }
}
