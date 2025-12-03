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
     * @var Criterion[]
     */
    private array $criteria = [];

    private ?int $limit = null;

    private ?int $offset = null;

    /**
     * @return Criterion[]
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * @param Criterion[] $criteria
     *
     * @return self Provides fluent interface
     */
    public function setCriteria(array $criteria): self
    {
        foreach ($criteria as $criterion) {
            $this->addCriterion($this->ensureObject(Criterion::class, $criterion));
        }

        return $this;
    }

    /**
     * @param Criterion $criterion
     *
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
     */
    public function setOffset(?int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'limit' => $this->limit,
            'offset' => $this->offset,
            'criteria' => $this->criteria,
        ]);
    }
}
