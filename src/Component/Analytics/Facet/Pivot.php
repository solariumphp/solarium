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

    private string $name;

    private string $expression;

    private ?Sort $sort;

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
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
     */
    public function setExpression(string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return Sort|null
     */
    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    /**
     * @param Sort|array|null $sort
     *
     * @return self Provides fluent interface
     */
    public function setSort(Sort|array|null $sort): self
    {
        $this->sort = $this->ensureObject(Sort::class, $sort);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'name' => $this->name,
            'expression' => $this->expression,
            'sort' => $this->sort,
        ]);
    }
}
