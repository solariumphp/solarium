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
 * Suggester query term result
 *
 * @package Solarium
 * @subpackage Result
 */
class Solarium_Result_Suggester_Term implements IteratorAggregate, Countable
{

    /**
     * NumFound
     *
     * @var int
     */
    protected $_numFound;

    /**
     * StartOffset
     *
     * @var int
     */
    protected $_startOffset;

    /**
     * EndOffset
     *
     * @var int
     */
    protected $_endOffset;

    /**
     * Suggestions
     *
     * @var array
     */
    protected $_suggestions;


    /**
     * Constructor
     *
     * @param int $numFound
     * @param int $startOffset
     * @param int $endOffset
     * @param array $suggestions
     */
    public function __construct($numFound, $startOffset, $endOffset, $suggestions)
    {
        $this->_numFound = $numFound;
        $this->_startOffset = $startOffset;
        $this->_endOffset = $endOffset;
        $this->_suggestions = $suggestions;
    }

    /**
     * Get NumFound
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->_numFound;
    }

    /**
     * Get StartOffset
     *
     * @return int
     */
    public function getStartOffset()
    {
        return $this->_startOffset;
    }

    /**
     * Get EndOffset
     *
     * @return int
     */
    public function getEndOffset()
    {
        return $this->_endOffset;
    }

    /**
     * Get suggestions
     *
     * @return array
     */
    public function getSuggestions()
    {
        return $this->_suggestions;
    }


    /**
     * IteratorAggregate implementation
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_suggestions);
    }

    /**
     * Countable implementation
     *
     * @return int
     */
    public function count()
    {
        return count($this->_suggestions);
    }

}