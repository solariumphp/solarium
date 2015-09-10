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

namespace Solarium\QueryType\Select\Query\Component\Stats;

use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Query\Component\AbstractComponent;
use Solarium\QueryType\Select\RequestBuilder\Component\Stats as RequestBuilder;
use Solarium\QueryType\Select\ResponseParser\Component\Stats as ResponseParser;
use Solarium\Exception\InvalidArgumentException;

/**
 * Stats component.
 *
 * @link http://wiki.apache.org/solr/StatsComponent
 */
class Stats extends AbstractComponent
{
    /**
     * Stats facets for all fields.
     *
     * @var array
     */
    protected $facets = array();

    /**
     * Fields.
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return SelectQuery::COMPONENT_STATS;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

    /**
     * Create a field instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the field
     * and it will be added to this query component.
     * If you supply an options array/object that contains a key the field will also be added to the component.
     *
     * When no key is supplied the field cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addField method.
     *
     * @param mixed $options
     *
     * @return Field
     */
    public function createField($options = null)
    {
        if (is_string($options)) {
            $fq = new Field();
            $fq->setKey($options);
        } else {
            $fq = new Field($options);
        }

        if ($fq->getKey() !== null) {
            $this->addField($fq);
        }

        return $fq;
    }

    /**
     * Add a field.
     *
     * Supports a field instance or a config array, in that case a new
     * field instance wil be created based on the options.
     *
     * @throws InvalidArgumentException
     *
     * @param Field|array $field
     *
     * @return self Provides fluent interface
     */
    public function addField($field)
    {
        if (is_array($field)) {
            $field = new Field($field);
        }

        $key = $field->getKey();

        if (0 === strlen($key)) {
            throw new InvalidArgumentException('A field must have a key value');
        }

        //double add calls for the same field are ignored, but non-unique keys cause an exception
        if (array_key_exists($key, $this->fields) && $this->fields[$key] !== $field) {
            throw new InvalidArgumentException('A field must have a unique key value');
        } else {
            $this->fields[$key] = $field;
        }

        return $this;
    }

    /**
     * Add multiple fields.
     *
     * @param array $fields
     *
     * @return self Provides fluent interface
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $key => $field) {
            // in case of a config array: add key to config
            if (is_array($field) && !isset($field['key'])) {
                $field['key'] = $key;
            }

            $this->addField($field);
        }

        return $this;
    }

    /**
     * Get a field.
     *
     * @param string $key
     *
     * @return string
     */
    public function getField($key)
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        } else {
            return;
        }
    }

    /**
     * Get all fields.
     *
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Remove a single field.
     *
     * You can remove a field by passing its key, or by passing the field instance
     *
     * @param string|Field $field
     *
     * @return self Provides fluent interface
     */
    public function removeField($field)
    {
        if (is_object($field)) {
            $field = $field->getKey();
        }

        if (isset($this->fields[$field])) {
            unset($this->fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields.
     *
     * @return self Provides fluent interface
     */
    public function clearFields()
    {
        $this->fields = array();

        return $this;
    }

    /**
     * Set multiple fields.
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
     * Specify a facet to return in the resultset.
     *
     * @param string $facet
     *
     * @return self Provides fluent interface
     */
    public function addFacet($facet)
    {
        $this->facets[$facet] = true;

        return $this;
    }

    /**
     * Specify multiple facets to return in the resultset.
     *
     * @param string|array $facets can be an array or string with comma
     *                             separated facetnames
     *
     * @return self Provides fluent interface
     */
    public function addFacets($facets)
    {
        if (is_string($facets)) {
            $facets = explode(',', $facets);
            $facets = array_map('trim', $facets);
        }

        foreach ($facets as $facet) {
            $this->addFacet($facet);
        }

        return $this;
    }

    /**
     * Remove a facet from the facet list.
     *
     * @param string $facet
     *
     * @return self Provides fluent interface
     */
    public function removeFacet($facet)
    {
        if (isset($this->facets[$facet])) {
            unset($this->facets[$facet]);
        }

        return $this;
    }

    /**
     * Remove all facets from the facet list.
     *
     * @return self Provides fluent interface
     */
    public function clearFacets()
    {
        $this->facets = array();

        return $this;
    }

    /**
     * Get the list of facets.
     *
     * @return array
     */
    public function getFacets()
    {
        return array_keys($this->facets);
    }

    /**
     * Set multiple facets.
     *
     * This overwrites any existing facets
     *
     * @param array $facets
     *
     * @return self Provides fluent interface
     */
    public function setFacets($facets)
    {
        $this->clearFacets();
        $this->addFacets($facets);

        return $this;
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'field':
                    $this->setFields($value);
                    break;
                case 'facet':
                    $this->setFacets($value);
                    break;
            }
        }
    }
}
