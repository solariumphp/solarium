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
 * @namespace
 */
namespace Solarium\Query\Select\Component\Facet;

/**
 * Facet base class
 *
 * @link http://wiki.apache.org/solr/SimpleFacetParameters
 *
 * @package Solarium
 * @subpackage Query
 */
abstract class Facet extends \Solarium\Configurable
{

    /**
     * Exclude tags for this facet
     *
     * @var array
     */
    protected $_excludes = array();

    /**
     * Must be implemented by the facet types and return one of the constants
     * 
     * @abstract
     * @return string
     */
    abstract public function getType();

    /**
     * Initialize options
     *
     * @return void
     */
    protected function _init()
    {
        foreach ($this->_options AS $name => $value) {
            switch ($name) {
                case 'key':
                    $this->setKey($value);
                    break;
                case 'exclude':
                    if(!is_array($value)) $value = array($value);
                    $this->setExcludes($value);
                    unset($this->_options['exclude']);
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
     * @return Solarium\Query\Select\Component\Facet Provides fluent interface
     */
    public function setKey($value)
    {
        return $this->_setOption('key', $value);
    }

    /**
     * Add an exclude tag
     *
     * @param string $tag
     * @return Solarium\Query\Select\Component\Facet Provides fluent interface
     */
    public function addExclude($tag)
    {
        $this->_excludes[$tag] = true;
        return $this;
    }

    /**
     * Add multiple exclude tags
     *
     * @param array $excludes
     * @return Solarium\Query\Select\Component\Facet Provides fluent interface
     */
    public function addExcludes(array $excludes)
    {
        foreach ($excludes AS $exclude) {
            $this->addExclude($exclude);
        }

        return $this;
    }

    /**
     * Get all excludes
     *
     * @return array
     */
    public function getExcludes()
    {
        return array_keys($this->_excludes);
    }

    /**
     * Remove a single exclude tag
     *
     * @param string $exclude
     * @return Solarium\Query\Select\Component\Facet Provides fluent interface
     */
    public function removeExclude($exclude)
    {
        if (isset($this->_excludes[$exclude])) {
            unset($this->_excludes[$exclude]);
        }

        return $this;
    }

    /**
     * Remove all excludes
     *
     * @return Solarium\Query\Select\Facet Provides fluent interface
     */
    public function clearExcludes()
    {
        $this->_excludes = array();
        return $this;
    }

    /**
     * Set multiple excludes
     *
     * This overwrites any existing excludes
     *
     * @param array $excludes
     */
    public function setExcludes($excludes)
    {
        $this->clearExcludes();
        $this->addExcludes($excludes);
    }
}