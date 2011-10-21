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
 * Facet query
 *
 * @link http://wiki.apache.org/solr/SimpleFacetParameters#Field_Value_Faceting_Parameters
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_Facet_Field extends Solarium_Query_Select_Component_Facet
{

    /**
     * Facet sort type index
     */
    const SORT_INDEX = 'index';

    /**
     * Facet sort type count
     */
    const SORT_COUNT = 'count';

    /**
     * Facet method enum
     */
    const METHOD_ENUM = 'enum';

    /**
     * Facet method fc
     */
    const METHOD_FC = 'fc';

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'field' => 'id'
    );

    /**
     * Get the facet type
     * 
     * @return string
     */
    public function getType()
    {
        return Solarium_Query_Select_Component_FacetSet::FACET_FIELD;
    }
    
    /**
     * Set the field name
     *
     * @param string $field
     * @return Solarium_Query_Select_Component_Facet_Field Provides fluent interface
     */
    public function setField($field)
    {
        return $this->_setOption('field', $field);
    }

    /**
     * Get the field name
     *
     * @return string
     */
    public function getField()
    {
        return $this->getOption('field');
    }

    /**
     * Set the facet sort order
     *
     * Use one of the SORT_* constants as the value
     *
     * @param string $sort
     * @return Solarium_Query_Select_Component_Facet_Field Provides fluent interface
     */
    public function setSort($sort)
    {
        return $this->_setOption('sort', $sort);
    }

    /**
     * Get the facet sort order
     *
     * @return string
     */
    public function getSort()
    {
        return $this->getOption('sort');
    }

    /**
     * Limit the terms for faceting by a prefix
     *
     * @param string $prefix
     * @return Solarium_Query_Select_Component_Facet_Field Provides fluent interface
     */
    public function setPrefix($prefix)
    {
        return $this->_setOption('prefix', $prefix);
    }

    /**
     * Get the facet prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->getOption('prefix');
    }

    /**
     * Set the facet limit
     *
     * @param int $limit
     * @return Solarium_Query_Select_Component_Facet_Field Provides fluent interface
     */
    public function setLimit($limit)
    {
        return $this->_setOption('limit', $limit);
    }

    /**
     * Get the facet limit
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->getOption('limit');
    }

    /**
     * Set the facet offset
     *
     * @param int $offset
     * @return Solarium_Query_Select_Component_Facet_Field Provides fluent interface
     */
    public function setOffset($offset)
    {
        return $this->_setOption('offset', $offset);
    }

    /**
     * Get the facet offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->getOption('offset');
    }

    /**
     * Set the facet mincount
     *
     * @param int $minCount
     * @return Solarium_Query_Select_Component_Facet_Field Provides fluent interface
     */
    public function setMinCount($minCount)
    {
        return $this->_setOption('mincount', $minCount);
    }

    /**
     * Get the facet mincount
     *
     * @return int
     */
    public function getMinCount()
    {
        return $this->getOption('mincount');
    }

    /**
     * Set the missing count option
     *
     * @param boolean $missing
     * @return Solarium_Query_Select_Component_Facet_Field Provides fluent interface
     */
    public function setMissing($missing)
    {
        return $this->_setOption('missing', $missing);
    }

    /**
     * Get the facet missing option
     *
     * @return boolean
     */
    public function getMissing()
    {
        return $this->getOption('missing');
    }

    /**
     * Set the facet method
     *
     * Use one of the METHOD_* constants as value
     *
     * @param string $method
     * @return Solarium_Query_Select_Facet_Field Provides fluent interface
     */
    public function setMethod($method)
    {
        return $this->_setOption('method', $method);
    }

    /**
     * Get the facet method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getOption('method');
    }

}