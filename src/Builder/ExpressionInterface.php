<?php

declare(strict_types=1);

namespace Solarium\Builder;

/**
 * Expression Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ExpressionInterface
{
    /**
     * @param \Solarium\Builder\AbstractExpressionVisitor $visitor
     *
     * @return mixed
     */
    public function visit(AbstractExpressionVisitor $visitor);
}
