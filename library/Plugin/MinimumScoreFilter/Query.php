<?php
/**
 * Copyright 2014 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

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
    protected $options = array(
        'handler'       => 'select',
        'resultclass'   => 'Solarium\QueryType\Select\Result\Result',
        'documentclass' => 'Solarium\QueryType\Select\Result\Document',
        'query'         => '*:*',
        'start'         => 0,
        'rows'          => 10,
        'fields'        => '*,score',
        'omitheader'    => true,
        'filterratio'  => 0.1,
        'filter_mode'   => self::FILTER_MODE_REMOVE,
    );

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
        if (!in_array('score', $fields)) {
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
