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
use Solarium\Component\Analytics\Facet\ObjectTrait;
use Solarium\Core\Configurable;

/**
 * Sort.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class Sort extends Configurable implements \JsonSerializable
{
    use ConfigurableInitTrait;
    use ObjectTrait;

    /**
     * @var \Solarium\Component\Analytics\Facet\Sort\Criterion[]
     */
    private $criteria = [];

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @return \Solarium\Component\Analytics\Facet\Sort\Criterion[]
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * @param \Solarium\Component\Analytics\Facet\Sort\Criterion[] $criteria
     *
     * @return $this
     */
    public function setCriteria(array $criteria): self
    {
        foreach ($criteria as $criterion) {
            $this->addCriterion($this->ensureObject(Criterion::class, $criterion));
        }

        return $this;
    }

    /**
     * @param \Solarium\Component\Analytics\Facet\Sort\Criterion $criterion
     *
     * @return $this
     */
    public function addCriterion(Criterion $criterion): self
    {
        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     *
     * @return $this
     */
    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     *
     * @return $this
     */
    public function setOffset(?int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter([
            'limit' => $this->limit,
            'offset' => $this->offset,
            'criteria' => $this->criteria,
        ]);
    }
}
