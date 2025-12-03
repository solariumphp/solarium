<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Analytics\Facet;

/**
 * Pivot Facet.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class PivotFacet extends AbstractFacet
{
    use ObjectTrait;

    /**
     * @var Pivot[]
     */
    private array $pivots = [];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return AbstractFacet::TYPE_PIVOT;
    }

    /**
     * @return Pivot[]
     */
    public function getPivots(): array
    {
        return $this->pivots;
    }

    /**
     * @param Pivot[]|array $pivots
     *
     * @return self Provides fluent interface
     */
    public function setPivots(array $pivots): self
    {
        foreach ($pivots as $pivot) {
            $this->addPivot($this->ensureObject(Pivot::class, $pivot));
        }

        return $this;
    }

    /**
     * @param Pivot $pivot
     *
     * @return self Provides fluent interface
     */
    public function addPivot(Pivot $pivot): self
    {
        $this->pivots[] = $pivot;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'type' => $this->getType(),
            'pivots' => $this->pivots,
        ]);
    }
}
