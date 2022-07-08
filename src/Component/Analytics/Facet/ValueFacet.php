<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics\Facet;

use Solarium\Component\Analytics\Facet\Sort\Sort;

/**
 * Value Facet.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class ValueFacet extends AbstractFacet
{
    use ObjectTrait;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var \Solarium\Component\Analytics\Facet\Sort\Sort|null
     */
    private $sort;

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return AbstractFacet::TYPE_VALUE;
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
     * @return \Solarium\Component\Analytics\Facet\Sort\Sort|null
     */
    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    /**
     * @param \Solarium\Component\Analytics\Facet\Sort\Sort|array|null $sort
     *
     * @return $this
     */
    public function setSort($sort): self
    {
        $this->sort = $this->ensureObject(Sort::class, $sort);

        return $this;
    }

    #[\ReturnTypeWillChange]
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter([
            'type' => $this->getType(),
            'expression' => $this->expression,
            'sort' => $this->sort,
        ]);
    }
}
