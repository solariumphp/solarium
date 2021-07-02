<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\Component\AbstractComponent;
use Solarium\QueryType\Select\Query\Query as SelectQuery;

/**
 * MinimumScoreFilter Query.
 *
 * Extends the standard select query and adds functionality for the minimumscore filter
 */
class Query extends SelectQuery
{
    /**
     * Filter mode mark documents.
     */
    const FILTER_MODE_MARK = 'mark';

    /**
     * Filter mode remove documents.
     */
    const FILTER_MODE_REMOVE = 'remove';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'select',
        'resultclass' => Result::class,
        'documentclass' => \Solarium\QueryType\Select\Result\Document::class,
        'query' => '*:*',
        'start' => 0,
        'rows' => 10,
        'fields' => '*,score',
        'omitheader' => true,
        'filterratio' => 0.1,
        'filter_mode' => self::FILTER_MODE_REMOVE,
    ];

    /**
     * Set filter mode.
     *
     * @param string $mode
     *
     * @return self Provides fluent interface
     */
    public function setFilterMode(string $mode): self
    {
        $this->setOption('filter_mode', $mode);

        return $this;
    }

    /**
     * Get filter mode.
     *
     * @return string|null
     */
    public function getFilterMode(): ?string
    {
        return $this->getOption('filter_mode');
    }

    /**
     * Set filterratio option.
     *
     * This should be a ratio between 0 and 1, the minimum score is calculated by multiplying maxscore with this ratio.
     *
     * @param float $value
     *
     * @return self Provides fluent interface
     */
    public function setFilterRatio(float $value): self
    {
        $this->setOption('filterratio', $value);

        return $this;
    }

    /**
     * Get filterratio option.
     *
     * @return float|null
     */
    public function getFilterRatio(): ?float
    {
        return $this->getOption('filterratio');
    }

    /**
     * Make sure the score field is always enabled.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = parent::getFields();
        if (!\in_array('score', $fields, true)) {
            $fields[] = 'score';
        }

        return $fields;
    }

    /**
     * Get all registered components.
     *
     * @return AbstractComponent[]
     */
    public function getComponents(): array
    {
        if (isset($this->components[self::COMPONENT_GROUPING])) {
            $this->components[self::COMPONENT_GROUPING]->setOption('resultquerygroupclass', QueryGroupResult::class);
            $this->components[self::COMPONENT_GROUPING]->setOption('resultvaluegroupclass', ValueGroupResult::class);
        }

        return parent::getComponents();
    }
}
