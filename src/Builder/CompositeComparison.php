<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Builder;

use Solarium\Exception\RuntimeException;

/**
 * Composite Expression.
 *
 * @codeCoverageIgnore
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class CompositeComparison implements ExpressionInterface
{
    public const TYPE_AND = 'AND';

    public const TYPE_OR = 'OR';

    /**
     * @var string
     */
    private $type;

    /**
     * @var \Solarium\Builder\Comparison[]
     */
    private $comparisons = [];

    /**
     * @param string $type
     * @param array  $comparisons
     *
     * @throws \Solarium\Exception\RuntimeException
     */
    public function __construct($type, array $comparisons)
    {
        $this->type = $type;

        foreach ($comparisons as $expr) {
            if ($expr instanceof Value) {
                throw new RuntimeException('Values are not supported expressions as children of and/or expressions.');
            }
            if (!($expr instanceof ExpressionInterface)) {
                throw new RuntimeException('No expression given to CompositeExpression.');
            }

            $this->comparisons[] = $expr;
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return \Solarium\Builder\Comparison[]
     */
    public function getComparisons(): array
    {
        return $this->comparisons;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(AbstractExpressionVisitor $visitor)
    {
        return $visitor->walkCompositeExpression($this);
    }
}
