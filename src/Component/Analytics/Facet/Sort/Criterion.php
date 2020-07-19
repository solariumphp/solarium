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

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var string
     */
    private $direction;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * @param string $expression
     *
     * @return $this
     */
    public function setExpression(string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @return $this
     */
    public function setDirection(string $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter([
            'type' => $this->type,
            'expression' => $this->expression,
            'direction' => $this->direction,
        ]);
    }
}
