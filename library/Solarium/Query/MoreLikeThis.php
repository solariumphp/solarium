<?php
/**
 * Copyright 2011 Bas de Nooijer.
 * Copyright 2011 Gasol Wu. PIXNET Digital Media Corporation.
 * All rights reserved.
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
 * @copyright Copyright 2011 Gasol Wu <gasol.wu@gmail.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * MoreLikeThis Query
 *
 * Can be used to select documents and/or facets from Solr. This querytype has
 * lots of options and there are many Solarium subclasses for it.
 * See the Solr documentation and the relevant Solarium classes for more info.
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_MoreLikeThis extends Solarium_Query
{

    /**
     * Query components
     */
    const COMPONENT_DISMAX = 'dismax';

    /**
     * Query component morelikethis
     */
    const COMPONENT_MORELIKETHIS = 'morelikethis';

    /**
     * Query component highlighting
     */
    const COMPONENT_HIGHLIGHTING = 'highlighting';

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Solarium_Client::QUERYTYPE_MORELIKETHIS;
    }

    /**
     * Default options
     * 
     * @var array
     */
    protected $_options = array(
        'handler'       => 'mlt',
        'resultclass'   => 'Solarium_Result_MoreLikeThis',
        'documentclass' => 'Solarium_Document_ReadOnly',
        'query'         => '*:*',
        'start'         => 0,
        'rows'          => 10,
        'fl'            => '*,score',
        'mlt.fl'        => 'text',
        'mlt.interestingTerms' => 'none',
        'mlt.match.include' => 'false',
        'stream'        => false
    );

    /**
     * Default select query component types
     * 
     * @var array
     */
    protected $_componentTypes = array(
        self::COMPONENT_DISMAX => array(
            'component' => 'Solarium_Query_Select_Component_DisMax',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Select_Component_DisMax',
            'responseparser' => null,
        ),
        self::COMPONENT_MORELIKETHIS => array(
            'component' => 'Solarium_Query_Select_Component_MoreLikeThis',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Select_Component_MoreLikeThis',
            'responseparser' => 'Solarium_Client_ResponseParser_Select_Component_MoreLikeThis',
        ),
        self::COMPONENT_HIGHLIGHTING => array(
            'component' => 'Solarium_Query_Select_Component_Highlighting',
            'requestbuilder' => 'Solarium_Client_RequestBuilder_Select_Component_Highlighting',
            'responseparser' => 'Solarium_Client_ResponseParser_Select_Component_Highlighting',
        ),
    );

    /**
     * Fields to fetch
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Filterqueries
     *
     * @var array
     */
    protected $_filterQueries = array();

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
                case 'fields':
                    $this->addFields($value);
                    break;
                case 'rows':
                    $this->setRows((int)$value);
                    break;
                case 'start':
                    $this->setStart((int)$value);
                    break;
                case 'component':
                    $this->_createComponents($value);
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
     * @param boolean $stream true for POST requests where the content-type is
     * not "application/x-www-form-urlencoded", the raw POST body is passed as a stream.
     * @link http://wiki.apache.org/solr/ContentStream ContentStream
     * @return Solarium_Query_Select Provides fluent interface
     */
    public function setQuery($query, $stream = false)
    {
        return $this->_setOption('stream', $stream)
                    ->_setOption('query', trim($query));
    }

    /**
     * @see setQuery
     * @return boolean
     */
    public function isStream()
    {
        return $this->getOption('stream');
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * Set the interestingTerms parameter.  Must be one of: none, list, details.
     *
     * @see http://wiki.apache.org/solr/MoreLikeThisHandler#Params
     * @param string $term
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
     */
    public function setInterestingTerms($term)
    {
        return $this->_setOption('mlt.interestingTerms', $term);
    }
    
    /**
     * Get the interestingTerm parameter.
     *
     * @return string
     */
    public function getInterestingTerms() 
    {
        return $this->getOption('mlt.interestingTerms');
    }
    
    /**
     * Set the match.include parameter, which is either 'true' or 'false'.  
     * 
     * @see http://wiki.apache.org/solr/MoreLikeThisHandler#Params
     * @return Solarium_Query_MoreLikeThis Provides fluent interface
     */
    public function setMatchInclude() 
    {
        return $this->_setOption('mlt.match.include', 'true');
    }
    
    /**
     * Get the match.include parameter.
     *
     * @return string
     */
    public function getMatchInclude() 
    {
        return $this->getOption('mlt.match.include');
    }
    
    /**
     * Set a custom resultclass
     *
     * @param string $value classname
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
     */
    public function setFields($fields)
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Create a filterquery instance
     *
     * @param mixed $options
     * @return Solarium_Query_Select_FilterQuery
     */
    public function createFilterQuery($options = null)
    {
        return new Solarium_Query_Select_FilterQuery($options);
    }

    /**
     * Add a filter query
     *
     * Supports a filterquery instance or a config array, in that case a new
     * filterquery instance wil be created based on the options.
     *
     * @param Solarium_Query_Select_FilterQuery|array $filterQuery
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * @return Solarium_Query_Select Provides fluent interface
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
     * Get all registered component types
     *
     * @return array
     */
    public function getComponentTypes()
    {
        return $this->_componentTypes;
    }

    /**
     * Register a component type
     *
     * @param string $key
     * @param string $component
     * @param string $requestBuilder
     * @param string $responseParser
     * @return Solarium_Query_Select Provides fluent interface
     */
    public function registerComponentType($key, $component, $requestBuilder=null, $responseParser=null)
    {
        $this->_componentTypes[$key] = array(
            'component' => $component,
            'requestbuilder' => $requestBuilder,
            'responseparser' => $responseParser,
        );

        return $this;
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
     * @param array $config Configuration to use for autoload
     * @return object|null
     */
    public function getComponent($key, $autoload = false, $config = null)
    {
        if (isset($this->_components[$key])) {
            return $this->_components[$key];
        } else {
            if ($autoload == true) {

                if (!isset($this->_componentTypes[$key])) {
                    throw new Solarium_Exception('Cannot autoload unknown component: ' . $key);
                }
                
                $className = $this->_componentTypes[$key]['component'];
                $component = new $className($config);
                $this->setComponent($key, $component);
                return $component;
            }
            return null;
        }
    }

    /**
     * Set a component instance
     *
     * This overwrites any existing component registered with the same key.
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
     * Remove a component instance
     *
     * @param string $key
     * @return Solarium_Query_Select Provides fluent interface
     */
    public function removeComponent($key)
    {
        if (isset($this->_components[$key])) {
            unset($this->_components[$key]);
        }
        return $this;
    }


    /**
     * Build component instances based on config
     *
     * @param array $configs
     * @return void
     */
    protected function _createComponents($configs)
    {
        foreach ($configs AS $type => $config) {
            $this->getComponent($type, true, $config);
        }
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
        return $this->getComponent(Solarium_Query_Select::COMPONENT_MORELIKETHIS, true);
    }

    /**
     * Get a FacetSet component instance
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Query_Select_Component_FacetSet
     */
    public function getFacetSet()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_FACETSET, true);
    }

    /**
     * Get a DisMax component instance
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Query_Select_Component_DisMax
     */
    public function getDisMax()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_DISMAX, true);
    }

    /**
     * Get a highlighting component instance
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Query_Select_Component_Highlighting
     */
    public function getHighlighting()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_HIGHLIGHTING, true);
    }

}
