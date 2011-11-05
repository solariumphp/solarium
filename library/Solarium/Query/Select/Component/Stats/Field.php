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
 *
 * TODO
 * Voorbeeld request:
 * http://localhost:8983/solr/select?q=*:*&stats=true&stats.field=price&stats.field=popularity
 *      &stats.twopass=true&rows=0&indent=true&stats.facet=inStock&f.price.stats.facet=price
 *      &f.price.stats.facet=popularity
 */

/**
 * Stats component field class
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_Stats_Field extends Solarium_Configurable
{

    /**
     * Field facets (for stats)
     *
     * @var array
     */
    protected $_facets = array();

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
                case 'facet':
                    $this->setFacets($value);
                    break;
            }
        }
    }

    /**
     * Get key value
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getOption('key');
    }

    /**
     * Set key value
     *
     * @param string $value
     * @return Solarium_Query_Select_Component_Stats Provides fluent interface
     */
    public function setKey($value)
    {
        return $this->_setOption('key', $value);
    }

    /**
     * Specify a facet to return in the resultset
     *
     * @param string $facet
     * @return Solarium_Query_Select_Component_Stats Provides fluent interface
     */
    public function addFacet($facet)
    {
       $this->_facets[$facet] = true;
       return $this;
    }

    /**
     * Specify multiple facets to return in the resultset
     *
     * @param string|array $facets can be an array or string with comma
     * separated facetnames
     *
     * @return Solarium_Query_Select_Component_Stats Provides fluent interface
     */
    public function addFacets($facets)
    {
        if (is_string($facets)) {
            $facets = explode(',', $facets);
            $facets = array_map('trim', $facets);
        }

        foreach ($facets AS $facet) {
            $this->addFacet($facet);
        }

        return $this;
    }

    /**
     * Remove a facet from the facet list
     *
     * @param string $facet
     * @return Solarium_Query_Select_Component_Stats Provides fluent interface
     */
    public function removeFacet($facet)
    {
        if (isset($this->_facets[$facet])) {
           unset($this->_facets[$facet]);
        }

        return $this;
    }

    /**
     * Remove all facets from the facet list.
     *
     * @return Solarium_Query_Select_Component_Stats Provides fluent interface
     */
    public function clearFacets()
    {
        $this->_facets = array();
        return $this;
    }

    /**
     * Get the list of facets
     *
     * @return array
     */
    public function getFacets()
    {
        return array_keys($this->_facets);
    }

    /**
     * Set multiple facets
     *
     * This overwrites any existing facets
     *
     * @param array $facets
     * @return Solarium_Query_Select_Component_Stats Provides fluent interface
     */
    public function setFacets($facets)
    {
        $this->clearFacets();
        $this->addFacets($facets);

        return $this;
    }

}