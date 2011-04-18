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
 * @package Solarium
 * @subpackage Query
 */

/**
 * Select Query
 *
 * Can be used to select documents and/or facets from Solr. This querytype has
 * lots of options and there are many Solarium subclasses for it.
 * See the Solr documentation and the relevant Solarium classes for more info.
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select extends Solarium_Query
{

    /**
     * Solr sort modes
     */
    const SORT_DESC = 'desc';
    const SORT_ASC = 'asc';

    /**
     * Default options
     * 
     * @var array
     */
    protected $_options = array(
        'handler'       => 'select',
        'resultclass'   => 'Solarium_Result_Select',
        'documentclass' => 'Solarium_Document_ReadOnly',
        'query'         => '*:*',
        'start'         => 0,
        'rows'          => 10,
        'fields'        => '*,score',
    );

    /**
     * Fields to fetch
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Fields to sort on
     *
     * @var array
     */
    protected $_sortFields = array();

    /**
     * Filterqueries
     *
     * @var array
     */
    protected $_filterQueries = array();

    /**
     * Facets
     *
     * @var array
     */
    protected $_facets = array();

    /**
     * Search components
     *
     * @var array
     */
    protected $_components = array();

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
                case 'query':
                    $this->setQuery($value);
                    break;
                case 'filterquery':
                    $this->addFilterQueries($value);
                    break;
                case 'facet':
                    $this->addFacets($value);
                    break;
                case 'sort':
                    $this->addSortFields($value);
                    break;
                case 'fields':
                    $this->addFields($value);
                    break;
                case 'rows':
                    $this->setRows((int)$value);
                    break;
                case 'start':
                    $this->setStart((int)$value);
                    break;
            }
        }
    }

    /**
     * Set the query string
     *
     * Overwrites the current value. You are responsible for the correct
     * escaping of user input.
     *
     * @param string $query
     * @return Solarium_Query Provides fluent interface
     */
    public function setQuery($query)
    {
        return $this->_setOption('query', trim($query));
    }

    /**
     * Get the query string
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->getOption('query');
    }

    /**
     * Set the start offset
     *
     * @param integer $start
     * @return Solarium_Query Provides fluent interface
     */
    public function setStart($start)
    {
        return $this->_setOption('start', $start);
    }

    /**
     * Get the start offset
     *
     * @return integer
     */
    public function getStart()
    {
        return $this->getOption('start');
    }

    /**
     * Set a custom resultclass
     *
     * @param string $value classname
     * @return Solarium_Query Provides fluent interface
     */
    public function setResultClass($value)
    {
        return $this->_setOption('resultclass', $value);
    }

    /**
     * Get the current resultclass option
     *
     * The value is a classname, not an instance
     *
     * @return string
     */
    public function getResultClass()
    {
        return $this->getOption('resultclass');
    }

    /**
     * Set a custom document class
     *
     * @param string $value classname
     * @return Solarium_Query
     */
    public function setDocumentClass($value)
    {
        return $this->_setOption('documentclass', $value);
    }

    /**
     * Get the current documentclass option
     *
     * The value is a classname, not an instance
     * 
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->getOption('documentclass');
    }

    /**
     * Set the number of rows to fetch
     *
     * @param integer $rows
     * @return Solarium_Query Provides fluent interface
     */
    public function setRows($rows)
    {
        return $this->_setOption('rows', $rows);
    }

    /**
     * Get the number of rows
     *
     * @return integer
     */
    public function getRows()
    {
        return $this->getOption('rows');
    }

    /**
     * Specify a field to return in the resultset
     *
     * @param string $field
     * @return Solarium_Query Provides fluent interface
     */
    public function addField($field)
    {
       $this->_fields[$field] = true;
       return $this;
    }

    /**
     * Specify multiple fields to return in the resultset
     *
     * @param string|array $fields can be an array or string with comma
     * separated fieldnames
     *
     * @return Solarium_Query Provides fluent interface
     */
    public function addFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        foreach ($fields AS $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Remove a field from the field list
     *
     * @param string $field
     * @return Solarium_Query Provides fluent interface
     */
    public function removeField($field)
    {
        if (isset($this->_fields[$field])) {
           unset($this->_fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields from the field list.
     *
     * @return Solarium_Query Provides fluent interface
     */
    public function clearFields()
    {
        $this->_fields = array();
        return $this;
    }

    /**
     * Get the list of fields
     *
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->_fields);
    }

    /**
     * Set multiple fields
     *
     * This overwrites any existing fields
     *
     * @param array $fields
     * @return Solarium_Query Provides fluent interface
     */
    public function setFields($fields)
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Add a sort field
     *
     * @param string $field
     * @param string $order
     * @return Solarium_Query Provides fluent interface
     */
    public function addSortField($field, $order)
    {
        $this->_sortFields[$field] = $order;

        return $this;
    }

    /**
     * Add multiple sort fields
     *
     * The input array must contain fieldnames as keys and the order as values.
     *
     * @param array $sortFields
     * @return Solarium_Query Provides fluent interface
     */
    public function addSortFields(array $sortFields)
    {
        foreach ($sortFields AS $sortField => $sortOrder) {
            $this->addSortField($sortField, $sortOrder);
        }

        return $this;
    }

    /**
     * Remove a sortfield
     *
     * @param string $field
     * @return Solarium_Query Provides fluent interface
     */
    public function removeSortField($field)
    {
        if (isset($this->_sortFields[$field])) {
            unset($this->_sortFields[$field]);
        }

        return $this;
    }

    /**
     * Remove all sortfields
     *
     * @return Solarium_Query Provides fluent interface
     */
    public function clearSortFields()
    {
        $this->_sortFields = array();
        return $this;
    }

    /**
     * Get a list of the sortfields
     *
     * @return array
     */
    public function getSortFields()
    {
        return $this->_sortFields;
    }

    /**
     * Set multiple sortfields
     *
     * This overwrites any existing sortfields
     *
     * @param array $fields
     * @return Solarium_Query Provides fluent interface
     */
    public function setSortFields($fields)
    {
        $this->clearSortFields();
        $this->addSortFields($fields);

        return $this;
    }

    /**
     * Add a filter query
     *
     * Supports a filterquery instance or a config array, in that case a new
     * filterquery instance wil be created based on the options.
     *
     * @param Solarium_Query_Select_FilterQuery|array $filterQuery
     * @return Solarium_Query Provides fluent interface
     */
    public function addFilterQuery($filterQuery)
    {
        if (is_array($filterQuery)) {
            $filterQuery = new Solarium_Query_Select_FilterQuery($filterQuery);
        }
        
        $key = $filterQuery->getKey();

        if (0 === strlen($key)) {
            throw new Solarium_Exception('A filterquery must have a key value');
        }

        if (array_key_exists($key, $this->_filterQueries)) {
            throw new Solarium_Exception('A filterquery must have a unique key'
                . ' value within a query');
        }

        $this->_filterQueries[$key] = $filterQuery;
        return $this;
    }

    /**
     * Add multiple filterqueries
     *
     * @param array $filterQueries
     * @return Solarium_Query Provides fluent interface
     */
    public function addFilterQueries(array $filterQueries)
    {
        foreach ($filterQueries AS $key => $filterQuery) {

            // in case of a config array: add key to config
            if (is_array($filterQuery) && !isset($filterQuery['key'])) {
                $filterQuery['key'] = $key;
            }

            $this->addFilterQuery($filterQuery);
        }

        return $this;
    }

    /**
     * Get a filterquery
     *
     * @param string $key
     * @return string
     */
    public function getFilterQuery($key)
    {
        if (isset($this->_filterQueries[$key])) {
            return $this->_filterQueries[$key];
        } else {
            return null;
        }
    }

    /**
     * Get all filterqueries
     *
     * @return array
     */
    public function getFilterQueries()
    {
        return $this->_filterQueries;
    }

    /**
     * Remove a single filterquery by key
     *
     * @param string $key
     * @return Solarium_Query Provides fluent interface
     */
    public function removeFilterQuery($key)
    {
        if (isset($this->_filterQueries[$key])) {
            unset($this->_filterQueries[$key]);
        }

        return $this;
    }

    /**
     * Remove all filterqueries
     *
     * @return Solarium_Query Provides fluent interface
     */
    public function clearFilterQueries()
    {
        $this->_filterQueries = array();
        return $this;
    }

    /**
     * Set multiple filterqueries
     *
     * This overwrites any existing filterqueries
     *
     * @param array $filterQueries
     */
    public function setFilterQueries($filterQueries)
    {
        $this->clearFilterQueries();
        $this->addFilterQueries($filterQueries);
    }

    /**
     * Add a facet
     *
     * @param Solarium_Query_Select_Facet|array $facet
     * @return Solarium_Query Provides fluent interface
     */
    public function addFacet($facet)
    {
        if (is_array($facet)) {
            $className = 'Solarium_Query_Select_Facet_'.ucfirst($facet['type']);
            $facet = new $className($facet);
        }

        $key = $facet->getKey();

        if (0 === strlen($key)) {
            throw new Solarium_Exception('A facet must have a key value');
        }

        if (array_key_exists($key, $this->_facets)) {
            throw new Solarium_Exception('A facet must have a unique key value'
                . ' within a query');
        }

        $this->_facets[$key] = $facet;
        return $this;
    }

    /**
     * Add multiple facets
     *
     * @param array $facets
     * @return Solarium_Query Provides fluent interface
     */
    public function addFacets(array $facets)
    {
        foreach ($facets AS $key => $facet) {

            // in case of a config array: add key to config
            if (is_array($facet) && !isset($facet['key'])) {
                $facet['key'] = $key;
            }

            $this->addFacet($facet);
        }

        return $this;
    }

    /**
     * Get a facet
     *
     * @param string $key
     * @return string
     */
    public function getFacet($key)
    {
        if (isset($this->_facets[$key])) {
            return $this->_facets[$key];
        } else {
            return null;
        }
    }

    /**
     * Get all facets
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->_facets;
    }

    /**
     * Remove a single facet by key
     *
     * @param string $key
     * @return Solarium_Query Provides fluent interface
     */
    public function removeFacet($key)
    {
        if (isset($this->_facets[$key])) {
            unset($this->_facets[$key]);
        }

        return $this;
    }

    /**
     * Remove all facets
     *
     * @return Solarium_Query Provides fluent interface
     */
    public function clearFacets()
    {
        $this->_facets = array();
        return $this;
    }

    /**
     * Set multiple facets
     *
     * This overwrites any existing facets
     *
     * @param array $facets
     */
    public function setFacets($facets)
    {
        $this->clearFacets();
        $this->addFacets($facets);
    }

    /**
     * Get all registered components
     * 
     * @return array
     */
    public function getComponents()
    {
        return $this->_components;
    }

    /**
     * Get a component instance by key
     *
     * You can optionally supply an autoload class to create a new component
     * instance if there is no registered component for the given key yet.
     *
     * @param string $key Use one of the constants
     * @param string $autoload Class to autoload if component needs to be created
     * @return object|null
     */
    public function getComponent($key, $autoload = null)
    {
        if (isset($this->_components[$key]) && $this->_components[$key] !== null) {
            return $this->_components[$key];
        } else {
            if ($autoload !== null) {
                $component = new $autoload;
                $this->setComponent($key, $component);
                return $this->_components[$key];
            }
            return null;
        }
    }

    /**
     * Set a component instance
     *
     * This overwrites any existing component registered with the same key.
     * If you want to remove a component use NULL as value.
     *
     * @param string $key
     * @param object|null $value
     * @return Solarium_Query_Select Provides fluent interface
     */
    public function setComponent($key, $value)
    {
        $this->_components[$key] = $value;
        return $this;
    }

    /**
     * Get a MoreLikeThis component instance
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Query_Select_Component_MoreLikeThis
     */
    public function getMoreLikeThis()
    {
        return $this->getComponent('MoreLikeThis', 'Solarium_Query_Select_Component_MoreLikeThis');
    }

}