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
 * @subpackage Result
 */

/**
 * Select component grouping field group result
 *
 * @since 2.1.0
 * 
 * @package Solarium
 * @subpackage Result
 */
class Solarium_Result_Select_Grouping_FieldGroup implements IteratorAggregate, Countable
{

    /**
     * Match count
     *
     * @var int
     */
    protected $_matches;

    /**
     * Number of groups
     *
     * @var int
     */
    protected $_numberOfGroups;

    /**
     * Value groups
     *
     * @var array
     */
    protected $_valueGroups;
    
    /**
     * Constructor
     *
     * @param int $matches
     * @param int $numberOfGroups
     * @param array $groups
     * @return void
     */
    public function __construct($matches, $numberOfGroups, $groups)
    {
        $this->_matches = $matches;
        $this->_numberOfGroups = $numberOfGroups;
        $this->_valueGroups = $groups;
    }

    /**
     * Get matches value
     * 
     * @return int
     */
    public function getMatches()
    {
        return $this->_matches;
    }

    /**
     * Get numberOfGroups value
     *
     * Only available if the numberofgroups option in the query was 'true'
     *
     * @return int
     */
    public function getNumberOfGroups()
    {
        return $this->_numberOfGroups;
    }

    /**
     * Get all value groups
     *
     * @return array
     */
    public function getValueGroups()
    {
        return $this->_valueGroups;
    }
    
    /**
     * IteratorAggregate implementation
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_valueGroups);
    }

    /**
     * Countable implementation
     *
     * @return int
     */
    public function count()
    {
        return count($this->_valueGroups);
    }
}