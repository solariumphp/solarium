<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\QueryParser\Model;

/**
 * SwitchCase.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class SwitchCase
{
    /**
     * @var string|null
     */
    private $field;

    /**
     * @var string
     */
    private $case;

    /**
     * @param string|null $field
     * @param string      $case
     */
    public function __construct(?string $field, string $case)
    {
        $this->field = $field;
        $this->case = $case;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->field) {
            return sprintf('case = %s', $this->case);
        }

        return sprintf('case.%s = %s', $this->field, $this->case);
    }

    /**
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getCase(): string
    {
        return $this->case;
    }
}
