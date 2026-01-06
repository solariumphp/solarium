<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Analytics;

/**
 * Grouping result.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Grouping
{
    private string $name;

    private array $facets = [];

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string  $name
     * @param Facet[] $facets
     *
     * @return self Provides fluent interface
     */
    public function addFacets(string $name, array $facets): self
    {
        $this->facets[$name] = $facets;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function getFacets(string $name): ?array
    {
        return $this->facets[$name] ?? null;
    }
}
