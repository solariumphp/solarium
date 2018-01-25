<?php

namespace Solarium\Plugin\MinimumScoreFilter;

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
        'resultclass' => 'Solarium\QueryType\Select\Result\Result',
        'documentclass' => 'Solarium\QueryType\Select\Result\Document',
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
    public function setFilterMode($mode)
    {
        return $this->setOption('filter_mode', $mode);
    }

    /**
     * Get filter mode.
     *
     * @return string
     */
    public function getFilterMode()
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
    public function setFilterRatio($value)
    {
        return $this->setOption('filterratio', $value);
    }

    /**
     * Get filterratio option.
     *
     * @return float
     */
    public function getFilterRatio()
    {
        return $this->getOption('filterratio');
    }

    /**
     * Make sure the score field is always enabled.
     *
     * @return array
     */
    public function getFields()
    {
        $fields = parent::getFields();
        if (!in_array('score', $fields, true)) {
            $fields[] = 'score';
        }

        return $fields;
    }

    /**
     * Make sure the filtering result class is always used.
     *
     * @return string
     */
    public function getResultClass()
    {
        return 'Solarium\Plugin\MinimumScoreFilter\Result';
    }

    /**
     * Get all registered components.
     *
     * @return AbstractComponent[]
     */
    public function getComponents()
    {
        if (isset($this->components[self::COMPONENT_GROUPING])) {
            $this->components[self::COMPONENT_GROUPING]->setOption('resultquerygroupclass', 'Solarium\Plugin\MinimumScoreFilter\QueryGroupResult');
            $this->components[self::COMPONENT_GROUPING]->setOption('resultvaluegroupclass', 'Solarium\Plugin\MinimumScoreFilter\ValueGroupResult');
        }

        return parent::getComponents();
    }
}
