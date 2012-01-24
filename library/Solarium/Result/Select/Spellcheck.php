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
 * Select component spellcheck result
 *
 * @package Solarium
 * @subpackage Result
 */
class Solarium_Result_Select_Spellcheck implements IteratorAggregate, Countable
{

    /**
     * Suggestions array
     *
     * @var array
     */
    protected $_suggestions;

    /**
     * Collation object
     *
     * @var Solarium_Result_Select_Spellcheck_Collation
     */
    protected $_collation;

    /**
     * Correctly spelled?
     *
     * @var boolean
     */
    protected $_correctlySpelled;

    /**
     * Constructor
     *
     * @param array $suggestions
     * @param Solarium_Result_Select_Spellcheck_Collation $collation
     * @param boolean $correctlySpelled
     * @return void
     */
    public function __construct($suggestions, $collation, $correctlySpelled)
    {
        $this->_suggestions = $suggestions;
        $this->_collation = $collation;
        $this->_correctlySpelled = $correctlySpelled;
    }

    /**
     * Get the collation result
     *
     * @return Solarium_Result_Select_Spellcheck_Collation
     */
    public function getCollation()
    {
        return $this->_collation;
    }

    /**
     * Get correctly spelled status
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return bool
     */
    public function getCorrectlySpelled()
    {
        return $this->_correctlySpelled;
    }

    /**
     * Get a result by key
     *
     * @param mixed $key
     * @return Solarium_Result_Select_Highlighting_Suggestion|null
     */
    public function getSuggestion($key)
    {
        if (isset($this->_suggestions[$key])) {
            return $this->_suggestions[$key];
        } else {
            return null;
        }
    }

    /**
     * Get all suggestions
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