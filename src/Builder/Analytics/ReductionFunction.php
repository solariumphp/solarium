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
use Solarium\Exception\RuntimeException;

/**
 * Reduction Function.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ReductionFunction implements FunctionInterface, ExpressionInterface
{
    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#count
     */
    public const COUNT = 'count';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#doc-count
     */
    public const DOC_COUNT = 'doc_count';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#missing
     */
    public const MISSING = 'missing';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#analytics-unique
     */
    public const UNIQUE = 'unique';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#sum
     */
    public const SUM = 'sum';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#variance
     */
    public const VARIANCE = 'variance';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#standard-deviation
     */
    public const STANDARD_DEVIATION = 'stddev';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#mean
     */
    public const MEAN = 'mean';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#weighted-mean
     */
    public const WEIGHTED_MEAN = 'wmean';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#minimum
     */
    public const MINIMUM = 'min';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#maximum
     */
    public const MAXIMUM = 'max';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#median
     */
    public const MEDIAN = 'median';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#percentile
     */
    public const PERCENTILE = 'percentile';

    /**
     * @see https://solr.apache.org/guide/analytics-reduction-functions.html#ordinal
     */
    public const ORDINAL = 'ordinal';

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
     *
     * @throws \Solarium\Exception\RuntimeException
     */
    public function __construct(string $type, array $arguments)
    {
        $this->type = $type;

        foreach ($arguments as $argument) {
            if ($argument instanceof self) {
                throw new RuntimeException(sprintf('No reduction function can be an argument of another reduction function %s', (string) $argument));
            }

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
