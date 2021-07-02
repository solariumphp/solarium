<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
