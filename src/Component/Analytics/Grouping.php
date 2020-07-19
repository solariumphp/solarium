<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics;

use Solarium\Component\Analytics\Facet\AbstractFacet;
use Solarium\Component\Analytics\Facet\ConfigurableInitTrait;
use Solarium\Component\Analytics\Facet\ObjectTrait;
use Solarium\Core\Configurable;

/**
 * Grouping.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Grouping extends Configurable implements \JsonSerializable
{
    use ConfigurableInitTrait;
    use ObjectTrait;

    /**
     * @var string
     */
    private $key;

    /**
     * An array of expressions.
     *
     * @var array
     */
    private $expressions = [];

    /**
     * An array of facets.
     *
     * @var \Solarium\Component\Analytics\Facet\AbstractFacet[]
     */
    private $facets = [];

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return array
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * @param array $expressions
     *
     * @return $this
     */
    public function setExpressions(array $expressions): self
    {
        foreach ($expressions as $key => $expression) {
            $this->addExpression($key, $expression);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $expression
     *
     * @return $this
     */
    public function addExpression(string $key, string $expression): self
    {
        $this->expressions[$key] = $expression;

        return $this;
    }

    /**
     * @return \Solarium\Component\Analytics\Facet\AbstractFacet[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }

    /**
     * @param array $facets
     *
     * @return $this
     */
    public function setFacets(array $facets): self
    {
        foreach ($facets as $facet) {
            $this->addFacet($this->ensureObject(AbstractFacet::class, $facet));
        }

        return $this;
    }

    /**
     * @param \Solarium\Component\Analytics\Facet\AbstractFacet $facet
     *
     * @return $this
     */
    public function addFacet(AbstractFacet $facet): self
    {
        $this->facets[$facet->getKey()] = $facet;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter([
            'expressions' => $this->expressions,
            'facets' => $this->facets,
        ]);
    }
}
