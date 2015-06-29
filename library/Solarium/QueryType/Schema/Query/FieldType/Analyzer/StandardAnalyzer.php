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
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Schema\Query\FieldType\Analyzer;

use Solarium\QueryType\Schema\Query\FieldType\Analyzer\Filter\Filter;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\Filter\FilterInterface;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer\Tokenizer;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer\TokenizerInterface;

/**
 * Class StandardAnalyzer
 * @author Beno!t POLASZEK
 */
class StandardAnalyzer implements AnalyzerInterface {

    protected $class;
    protected $tokenizer;
    protected $filters = array();

    /**
     * Analyzer type. Can be 'analyzer', 'indexAnalyzer', or 'queryAnalyzer'
     * @see https://cwiki.apache.org/confluence/display/solr/Analyzers
     * @return string|null
     */
    public function getType() {
        return 'analyzer';
    }

    /**
     * @return string|null
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this - Provides Fluent Interface
     */
    public function setClass($class) {
        $this->class = $class;
        return $this;
    }

    /**
     * @return TokenizerInterface
     */
    public function getTokenizer() {
        return $this->tokenizer;
    }

    /**
     * @param TokenizerInterface  $tokenizer
     * @return $this - Provides Fluent Interface
     */
    public function setTokenizer(TokenizerInterface $tokenizer) {
        $this->tokenizer = $tokenizer;
        return $this;
    }

    /**
     * @param null $class
     * @param null $delimiter
     * @return Tokenizer
     */
    public function createTokenizer($class = null, $delimiter = null) {
        $tokenizer = new Tokenizer($class, $delimiter);
        $this->setTokenizer($tokenizer);
        return $tokenizer;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters() {
        return $this->filters;
    }

    /**
     * @param FilterInterface $filter
     * @return $this
     */
    public function addFilter(FilterInterface $filter) {
        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @param FilterInterface[] $filters
     * @return $this - Provides Fluent Interface
     */
    public function setFilters(array $filters) {
        $this->filters = array();
        foreach ($filters AS $filter)
            $this->addFilter($filter);
        return $this;
    }

    /**
     * @param       $class
     * @param array $attributes
     * @return Filter
     */
    public function createFilter($class, $attributes = array()) {
        $filter = new Filter($class, $attributes);
        $this->addFilter($filter);
        return $filter;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function castAsArray() {

        $out = array('class' => $this->getClass());

        if ($this->getTokenizer()) {
            $out['tokenizer'] = $this->getTokenizer()->castAsArray();
        }

        if ($this->getFilters()) {
            $out['filters'] = array_map(function (FilterInterface $filter) {
                return $filter->castAsArray();
            }, $this->getFilters());
        }

        return $out;
    }

}