<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;

/**
 * Facet pivot.
 *
 * @see http://wiki.apache.org/solr/SimpleFacetParameters#Pivot_.28ie_Decision_Tree.29_Faceting
 */
class Pivot extends AbstractFacet implements ExcludeTagsInterface
{
    use ExcludeTagsTrait;

    /**
     * Fields to use.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Optional stats.
     *
     * @var array
     */
    protected $stats = [];

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
        if (is_string($fields)) {
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
     * @return self Provides fluent interface
     */
    public function addStat(string $stat): self
    {
        $this->stats[$stat] = true;

        return $this;
    }

    /**
     * Specify multiple Stats.
     *
     * @param string|array $stats can be an array or string with comma
     *                            separated statnames
     *
     * @return self Provides fluent interface
     */
    public function addStats($stats): self
    {
        if (is_string($stats)) {
            $stats = explode(',', $stats);
            $stats = array_map('trim', $stats);
        }

        foreach ($stats as $stat) {
            $this->addStat($stat);
        }

        return $this;
    }

    /**
     * Remove a stat from the stats list.
     *
     * @param string $stat
     *
     * @return self Provides fluent interface
     */
    public function removeStat($stat): self
    {
        if (isset($this->stats[$stat])) {
            unset($this->stats[$stat]);
        }

        return $this;
    }

    /**
     * Remove all stats from the stats list.
     *
     * @return self Provides fluent interface
     */
    public function clearStats(): self
    {
        $this->stats = [];

        return $this;
    }

    /**
     * Get the list of stats.
     *
     * @return array
     */
    public function getStats(): array
    {
        return array_keys($this->stats);
    }

    /**
     * Set multiple stats.
     *
     * This overwrites any existing stats
     *
     * @param array $stats
     *
     * @return self Provides fluent interface
     */
    public function setStats(array $stats): self
    {
        $this->clearStats();
        $this->addStats($stats);

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
                case 'stats':
                    $this->setStats($value);
                    break;
            }
        }
    }
}
