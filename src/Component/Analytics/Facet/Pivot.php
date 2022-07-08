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
use Solarium\Core\Configurable;

/**
 * Pivot.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Pivot extends Configurable implements \JsonSerializable
{
    use ConfigurableInitTrait;
    use ObjectTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var \Solarium\Component\Analytics\Facet\Sort\Sort|null
     */
    private $sort;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

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
            'name' => $this->name,
            'expression' => $this->expression,
            'sort' => $this->sort,
        ]);
    }
}
