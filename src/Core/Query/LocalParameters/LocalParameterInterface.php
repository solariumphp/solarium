<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\LocalParameters;

/**
 * Local Parameter Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface LocalParameterInterface
{
    /**
     * Should return a key=value combo.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setValues(array $values): self;

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @return $this
     */
    public function clearValues(): self;

    /**
     * @param $value
     *
     * @return $this
     */
    public function addValue($value): self;

    /**
     * @param array $values
     *
     * @return $this
     */
    public function addValues(array $values): self;

    /**
     * @param $value
     *
     * @return $this
     */
    public function removeValue($value): self;
}
