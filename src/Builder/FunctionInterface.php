<?php

declare(strict_types=1);

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
