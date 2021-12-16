<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;
use Solarium\Exception\OutOfBoundsException;

/**
 * Facet pivot.
 *
 * @see https://solr.apache.org/guide/faceting.html#pivot-decision-tree-faceting
 */
class Pivot extends AbstractFacet
{
    use PivotMinCountTrait;

    /**
     * Facet sort type count.
     */
    public const SORT_COUNT = FieldValueParametersInterface::SORT_COUNT;

    /**
     * Facet sort type index.
     */
    public const SORT_INDEX = FieldValueParametersInterface::SORT_INDEX;

    /**
     * Fields to use.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Get the facet type.
     *
     * @return string
     */
    public function getType(): string
    {
        return FacetSetInterface::FACET_PIVOT;
    }

    /**
     * Set the minimum number of documents that need to match in order for the facet to be included in results.
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     *
     * @deprecated This method no longer has effect. Use {@link Solarium\Component\FacetSet::setPivotMinCount()} to
     *    set the global minCount or {@link setPivotMinCount()} to set the minCount for specific pivot fields instead.
     */
    public function setMinCount(int $minCount): self
    {
        $this->setOption('mincount', $minCount);

        return $this;
    }

    /**
     * Get the minimum number of documents that need to match in order for the facet to be included in results.
     *
     * @return int|null
     *
     * @deprecated
     */
    public function getMinCount(): ?int
    {
        return $this->getOption('mincount');
    }

    /**
     * Set the facet limit.
     *
     * @param int $limit
     *
     * @return self Provides fluent interface
     */
    public function setLimit(int $limit): self
    {
        $this->setOption('limit', $limit);

        return $this;
    }

    /**
     * Get the facet limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->getOption('limit');
    }

    /**
     * Set the facet offset.
     *
     * @param int $offset
     *
     * @return self Provides fluent interface
     */
    public function setOffset(int $offset): self
    {
        $this->setOption('offset', $offset);

        return $this;
    }

    /**
     * Get the facet offset.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->getOption('offset');
    }

    /**
     * Set the facet sort type.
     *
     * Use one of the SORT_* constants as the value.
     *
     * @param string $sort
     *
     * @return self Provides fluent interface
     */
    public function setSort(string $sort): self
    {
        $this->setOption('sort', $sort);

        return $this;
    }

    /**
     * Get the facet sort order.
     *
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->getOption('sort');
    }

    /**
     * Set the facet overrequest count.
     *
     * @param int $count
     *
     * @return self Provides fluent interface
     */
    public function setOverrequestCount($count): self
    {
        return $this->setOption('overrequest.count', $count);
    }

    /**
     * Get the facet overrequest count.
     *
     * @return int|null
     */
    public function getOverrequestCount(): ?int
    {
        return $this->getOption('overrequest.count');
    }

    /**
     * Set the facet overrequest ratio.
     *
     * @param float $ratio
     *
     * @return self Provides fluent interface
     */
    public function setOverrequestRatio($ratio): self
    {
        return $this->setOption('overrequest.ratio', $ratio);
    }

    /**
     * Get the facet overrequest ratio.
     *
     * @return float|null
     */
    public function getOverrequestRatio(): ?float
    {
        return $this->getOption('overrequest.ratio');
    }

    /**
     * Specify a field to return in the resultset.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function addField(string $field): self
    {
        $field = trim($field);
        $this->fields[$field] = true;

        return $this;
    }

    /**
     * Specify multiple fields to return in the resultset.
     *
     * @param string|array $fields can be an array or string with comma
     *                             separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function addFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
        }

        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Remove a field from the field list.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function removeField($field): self
    {
        if (isset($this->fields[$field])) {
            unset($this->fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields from the field list.
     *
     * @return self Provides fluent interface
     */
    public function clearFields(): self
    {
        $this->fields = [];

        return $this;
    }

    /**
     * Get the list of fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return array_keys($this->fields);
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param array|string $fields can be an array or string with comma
     *                             separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Add stat.
     *
     * @param string $stat
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function addStat(string $stat): self
    {
        $this
            ->getLocalParameters()
            ->setStat($stat)
        ;

        return $this;
    }

    /**
     * Specify multiple Stats.
     *
     * @param string|array $stats can be an array or string with comma
     *                            separated statnames
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function addStats($stats): self
    {
        if (false === \is_array($stats)) {
            $stats = array_map('trim', explode(',', $stats));
        }

        $this
            ->getLocalParameters()
            ->addStats($stats)
        ;

        return $this;
    }

    /**
     * Remove a stat from the stats list.
     *
     * @param string $stat
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function removeStat($stat): self
    {
        $this
            ->getLocalParameters()
            ->removeStat($stat)
        ;

        return $this;
    }

    /**
     * Remove all stats from the stats list.
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function clearStats(): self
    {
        $this
            ->getLocalParameters()
            ->clearStats()
        ;

        return $this;
    }

    /**
     * Get the list of stats.
     *
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this
            ->getLocalParameters()
            ->getStats()
        ;
    }

    /**
     * Set multiple stats.
     *
     * This overwrites any existing stats
     *
     * @param array $stats
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function setStats(array $stats): self
    {
        $this
            ->getLocalParameters()
            ->clearStats()
            ->addStats($stats)
        ;

        return $this;
    }

    /**
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'fields':
                    $this->addFields($value);
                    break;
            }
        }
    }
}
