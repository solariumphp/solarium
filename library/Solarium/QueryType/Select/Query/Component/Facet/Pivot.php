<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
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

namespace Solarium\QueryType\Select\Query\Component\Facet;

use Solarium\QueryType\Select\Query\Component\FacetSet;

/**
 * Facet pivot.
 *
 * @link http://wiki.apache.org/solr/SimpleFacetParameters#Pivot_.28ie_Decision_Tree.29_Faceting
 */
class Pivot extends AbstractFacet
{
    /**
     * Fields to use.
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Optional stats
     *
     * @var array
     */
    protected $stats = array();

    /**
     * Get the facet type
     *
     * @return string
     */
    public function getType()
    {
        return FacetSet::FACET_PIVOT;
    }

    /**
     * Set the facet mincount.
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setMinCount($minCount)
    {
        return $this->setOption('mincount', $minCount);
    }

    /**
     * Get the facet mincount.
     *
     * @return int
     */
    public function getMinCount()
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
    public function addField($field)
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
    public function addFields($fields)
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
    public function removeField($field)
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
    public function clearFields()
    {
        $this->fields = array();

        return $this;
    }

    /**
     * Get the list of fields.
     *
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->fields);
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields)
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Add stat
     *
     * @param string $stat
     * @return self  Provides fluent interface
     */
    public function addStat($stat)
    {
        $this->stats[$stat] = true;

        return $this;
    }

    /**
     * Specify multiple Stats
     *
     * @param string|array $stats can be an array or string with comma
     *                             separated statnames
     *
     * @return self Provides fluent interface
     */
    public function addStats($stats)
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
     * Remove a stat from the stats list
     *
     * @param  string $stat
     * @return self   Provides fluent interface
     */
    public function removeStat($stat)
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
    public function clearStats()
    {
        $this->stats = array();

        return $this;
    }

    /**
     * Get the list of stats
     *
     * @return array
     */
    public function getStats()
    {
        return array_keys($this->stats);
    }

    /**
     * Set multiple stats
     *
     * This overwrites any existing stats
     *
     * @param  array $stats
     * @return self  Provides fluent interface
     */
    public function setStats($stats)
    {
        $this->clearStats();
        $this->addStats($stats);

        return $this;
    }

    /**
     * Initialize options
     *
     * @return void
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
