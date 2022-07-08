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
     * @var \Solarium\Component\Analytics\Facet\Pivot[]
     */
    private $pivots = [];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return AbstractFacet::TYPE_PIVOT;
    }

    /**
     * @return \Solarium\Component\Analytics\Facet\Pivot[]
     */
    public function getPivots(): array
    {
        return $this->pivots;
    }

    /**
     * @param \Solarium\Component\Analytics\Facet\Pivot[]|array $pivots
     *
     * @return $this
     */
    public function setPivots(array $pivots): self
    {
        foreach ($pivots as $pivot) {
            $this->addPivot($this->ensureObject(Pivot::class, $pivot));
        }

        return $this;
    }

    /**
     * @param \Solarium\Component\Analytics\Facet\Pivot $pivot
     *
     * @return $this
     */
    public function addPivot(Pivot $pivot): self
    {
        $this->pivots[] = $pivot;

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
            'pivots' => $this->pivots,
        ]);
    }
}
