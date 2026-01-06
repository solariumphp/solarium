<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics\Facet\Sort;

use Solarium\Component\Analytics\Facet\ConfigurableInitTrait;
use Solarium\Core\Configurable;

/**
 * Criterion.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Criterion extends Configurable implements \JsonSerializable
{
    use ConfigurableInitTrait;

    /**
     * Sort by the value of an expression defined in the same grouping.
     */
    public const TYPE_EXPRESSION = 'expression';

    /**
     * Sort by the string-representation of the facet value.
     */
    public const TYPE_VALUE = 'facetvalue';

    /**
     * Ascending.
     */
    public const DIRECTION_ASCENDING = 'ascending';

    /**
     * Descending.
     */
    public const DIRECTION_DESCENDING = 'descending';

    private ?string $type = null;

    private ?string $expression = null;

    private ?string $direction = null;

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self Provides fluent interface
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getExpression(): ?string
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     *
     * @return self Provides fluent interface
     */
    public function setExpression(string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDirection(): ?string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @return self Provides fluent interface
     */
    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'type' => $this->type,
            'expression' => $this->expression,
            'direction' => $this->direction,
        ]);
    }
}
