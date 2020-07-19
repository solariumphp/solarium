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
 * @see https://lucene.apache.org/solr/guide/faceting.html#pivot-decision-tree-faceting
 */
class Pivot extends AbstractFacet
{
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
     * Set the facet limit.
     *
     * @param int $limit
     *
     * @return self Provides fluent interface
     */
    public function setLimit($limit): self
    {
        return $this->setOption('limit', $limit);
    }

    /**
     * Get the facet limit.
     *
     * @return int
     */
    public function getLimit(): ?int
    {
        return $this->getOption('limit');
    }

    /**
     * Set the facet mincount.
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setMinCount($minCount): self
    {
        $this->setOption('mincount', $minCount);

        return $this;
    }

    /**
     * Get the facet mincount.
     *
     * @return int|null
     */
    public function getMinCount(): ?int
    {
        return $this->getOption('mincount');
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
