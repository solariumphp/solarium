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
 * Reduction Function Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface FunctionInterface
{
    /**
     * @return string
     */
    public function __toString(): string;
}
