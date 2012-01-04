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
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * Grouping component
 *
 * Also known as Result Grouping or Field Collapsing.
 * See the Solr wiki for more info about this functionality
 *
 * @link http://wiki.apache.org/solr/FieldCollapsing
 *
 * @since 2.1.0
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_Grouping extends Solarium_Query_Select_Component
{

    /**
     * Value for format grouped
     */
    const FORMAT_GROUPED = 'grouped';

    /**
     * Value for format simple
     */
    const FORMAT_SIMPLE = 'simple';

    /**
     * Component type
     *
     * @var string
     */
    protected $_type = Solarium_Query_Select::COMPONENT_GROUPING;

    /**
     * Fields for grouping
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Queries for grouping
     *
     * @var array
     */
    protected $_queries = array();

    /**
     * Initialize options
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     *
     * @return void
     */
    protected function _init()
    {
        foreach ($this->_options AS $name => $value) {
            switch ($name) {
                case 'queries':
                    $this->setQueries($value);
                    break;
                case 'fields':
                    $this->setFields($value);
                    break;
            }
        }
    }

    /**
     * Add a grouping field
     *
     * Group based on the unique values of a field
     *
     * @param string $field
     * @return Solarium_Field_Select_Component_Grouping fluent interface
     */
    public function addField($field)
    {
        $this->_fields[] = $field;

        return $this;
    }

    /**
     * Add multiple grouping fields
     *
     * You can use an array or a comma separated string as input
     *
     * @param array|string $fields
     * @return Solarium_Field_Select_Component_Grouping Provides fluent interface
     */
    public function addFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $this->_fields = array_merge($this->_fields, $fields);

        return $this;
    }

    /**
     * Get all fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Remove all fields
     *
     * @return Solarium_Field_Select_Component_Grouping fluent interface
     */
    public function clearFields()
    {
        $this->_fields = array();
        return $this;
    }

    /**
     * Set multiple fields
     *
     * This overwrites any existing fields
     *
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->clearFields();
        $this->addFields($fields);
    }

    /**
     * Add a grouping query
     *
     * Group documents that match the given query
     *
     * @param string $query
     * @return Solarium_Query_Select_Component_Grouping fluent interface
     */
    public function addQuery($query)
    {
        $this->_queries[] = $query;

        return $this;
    }

    /**
     * Add multiple grouping queries
     *
     * @param array|string $queries
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function addQueries($queries)
    {
        if(!is_array($queries)) $queries = array($queries);

        $this->_queries = array_merge($this->_queries, $queries);

        return $this;
    }

    /**
     * Get all queries
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->_queries;
    }

    /**
     * Remove all queries
     *
     * @return Solarium_Query_Select_Component_Grouping fluent interface
     */
    public function clearQueries()
    {
        $this->_queries = array();
        return $this;
    }

    /**
     * Set multiple queries
     *
     * This overwrites any existing queries
     *
     * @param array $queries
     */
    public function setQueries($queries)
    {
        $this->clearQueries();
        $this->addQueries($queries);
    }

    /**
     * Set limit option
     *
     * The number of results (documents) to return for each group
     *
     * @param int $limit
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function setLimit($limit)
    {
        return $this->_setOption('limit', $limit);
    }

    /**
     * Get limit option
     *
     * @return string|null
     */
    public function getLimit()
    {
        return $this->getOption('limit');
    }

    /**
     * Set offset option
     *
     * The offset into the document list of each group.
     *
     * @param int $offset
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function setOffset($offset)
    {
        return $this->_setOption('offset', $offset);
    }

    /**
     * Get offset option
     *
     * @return string|null
     */
    public function getOffset()
    {
        return $this->getOption('offset');
    }

    /**
     * Set sort option
     *
     * How to sort documents within a single group
     *
     * @param string $sort
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function setSort($sort)
    {
        return $this->_setOption('sort', $sort);
    }

    /**
     * Get sort option
     *
     * @return string|null
     */
    public function getSort()
    {
        return $this->getOption('sort');
    }

    /**
     * Set mainresult option
     *
     * If true, the result of the first field grouping command is used as the main
     * result list in the response, using group format 'simple'
     *
     * @param boolean $value
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function setMainResult($value)
    {
        return $this->_setOption('mainresult', $value);
    }

    /**
     * Get mainresult option
     *
     * @return boolean|null
     */
    public function getMainResult()
    {
        return $this->getOption('mainresult');
    }

    /**
     * Set numberofgroups option
     *
     * If true, includes the number of groups that have matched the query.
     *
     * @param boolean $value
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function setNumberOfGroups($value)
    {
        return $this->_setOption('numberofgroups', $value);
    }

    /**
     * Get numberofgroups option
     *
     * @return boolean|null
     */
    public function getNumberOfGroups()
    {
        return $this->getOption('numberofgroups');
    }

    /**
     * Set cachepercentage option
     *
     * If > 0 enables grouping cache. Grouping is executed actual two searches.
     * This option caches the second search. A value of 0 disables grouping caching.
     *
     * Tests have shown that this cache only improves search time with boolean queries,
     * wildcard queries and fuzzy queries. For simple queries like a term query or
     * a match all query this cache has a negative impact on performance
     *
     * @param integer $value
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function setCachePercentage($value)
    {
        return $this->_setOption('cachepercentage', $value);
    }

    /**
     * Get cachepercentage option
     *
     * @return integer|null
     */
    public function getCachePercentage()
    {
        return $this->getOption('cachepercentage');
    }

    /**
     * Set truncate option
     *
     * If true, facet counts are based on the most relevant document of each group matching the query.
     * Same applies for StatsComponent. Default is false. Only available from Solr 3.4
     *
     * @param boolean $value
     * @return Solarium_Query_Select_Component_Grouping Provides fluent interface
     */
    public function setTruncate($value)
    {
        return $this->_setOption('truncate', $value);
    }

    /**
     * Get truncate option
     *
     * @return boolean|null
     */
    public function getTruncate()
    {
        return $this->getOption('truncate');
    }

}