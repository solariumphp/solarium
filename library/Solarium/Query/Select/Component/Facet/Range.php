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
 * Facet range
 *
 * @link http://wiki.apache.org/solr/SimpleFacetParameters#Facet_by_Range
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_Facet_Range extends Solarium_Query_Select_Component_Facet
{

    /**
     * Value for the 'other' option
     */
    const OTHER_BEFORE = 'before';

    /**
     * Value for the 'other' option
     */
    const OTHER_AFTER = 'after';

    /**
     * Value for the 'other' option
     */
    const OTHER_BETWEEN = 'between';

    /**
     * Value for the 'other' option
     */
    const OTHER_ALL = 'all';

    /**
     * Value for the 'other' option
     */
    const OTHER_NONE = 'none';

    /**
     * Value for the 'include' option
     */
    const INCLUDE_LOWER = 'lower';

    /**
     * Value for the 'include' option
     */
    const INCLUDE_UPPER = 'upper';

    /**
     * Value for the 'include' option
     */
    const INCLUDE_EDGE = 'edge';

    /**
     * Value for the 'include' option
     */
    const INCLUDE_OUTER = 'outer';

    /**
     * Value for the 'include' option
     */
    const INCLUDE_ALL = 'all';

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
        parent::_init();

        foreach ($this->_options AS $name => $value) {
            switch ($name) {
                case 'include':
                    $this->setInclude($value);
                    break;
                case 'other':
                    $this->setOther($value);
                    break;
            }
        }
    }

    /**
     * Get the facet type
     *
     * @return string
     */
    public function getType()
    {
        return Solarium_Query_Select_Component_FacetSet::FACET_RANGE;
    }
    
    /**
     * Set the field name
     *
     * @param string $field
     * @return Solarium_Query_Select_Component_Facet_Range Provides fluent interface
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
     * Set the lower bound of the range
     *
     * @param string $start
     * @return Solarium_Query_Select_Component_Facet_Range Provides fluent interface
     */
    public function setStart($start)
    {
        return $this->_setOption('start', $start);
    }

    /**
     * Get the lower bound of the range
     *
     * @return string
     */
    public function getStart()
    {
        return $this->getOption('start');
    }

    /**
     * Set the upper bound of the range
     *
     * @param string $end
     * @return Solarium_Query_Select_Component_Facet_Range Provides fluent interface
     */
    public function setEnd($end)
    {
        return $this->_setOption('end', $end);
    }

    /**
     * Get the upper bound of the range
     *
     * @return string
     */
    public function getEnd()
    {
        return $this->getOption('end');
    }

    /**
     * Set range gap
     *
     * The size of each range expressed as a value to be added to the lower bound
     *
     * @param string $gap
     * @return Solarium_Query_Select_Component_Facet_Range Provides fluent interface
     */
    public function setGap($gap)
    {
        return $this->_setOption('gap', $gap);
    }

    /**
     * Get range gap
     *
     * The size of each range expressed as a value to be added to the lower bound
     *
     * @return string
     */
    public function getGap()
    {
        return $this->getOption('gap');
    }

    /**
     * Set hardend option
     *
     * A Boolean parameter instructing Solr what to do in the event that facet.range.gap
     * does not divide evenly between facet.range.start and facet.range.end
     *
     * @param boolean $hardend
     * @return Solarium_Query_Select_Component_Facet_Range Provides fluent interface
     */
    public function setHardend($hardend)
    {
        return $this->_setOption('hardend', $hardend);
    }

    /**
     * Get hardend option
     *
     * @return boolean
     */
    public function getHardend()
    {
        return $this->getOption('hardend');
    }

    /**
     * Set other counts
     *
     * Use one of the constants as value.
     * If you want to use multiple values supply an array or comma separated string
     *
     * @param string|array $other
     * @return Solarium_Query_Select_Component_Facet_Range Provides fluent interface
     */
    public function setOther($other)
    {
        if (is_array($other)) {
            $other = implode(',', $other);
        }

        return $this->_setOption('other', $other);   
    }

    /**
     * Get other counts
     *
     * In case of multiple values a comma separated string will be returned
     *
     * @return string
     */
    public function getOther()
    {
        return $this->getOption('other');
    }

    /**
     * Set include option
     *
     * Use one of the constants as value.
     * If you want to use multiple values supply an array or comma separated string
     *
     * @param string|array $include
     * @return Solarium_Query_Select_Component_Facet_Range Provides fluent interface
     */
    public function setInclude($include)
    {
        if (is_array($include)) {
            $include = implode(',', $include);
        }

        return $this->_setOption('include', $include);
    }

    /**
     * Get include option
     *
     * In case of multiple values a comma separated string will be returned
     *
     * @return string
     */
    public function getInclude()
    {
        return $this->getOption('include');
    }
}