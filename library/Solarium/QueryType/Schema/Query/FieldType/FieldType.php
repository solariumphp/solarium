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
namespace Solarium\QueryType\Schema\Query\FieldType;

use Solarium\Core\StringableInterface;
use Solarium\Exception\OutOfBoundsException;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\AnalyzerInterface;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\IndexAnalyzer;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\QueryAnalyzer;
use Solarium\QueryType\Schema\Query\FieldType\Analyzer\StandardAnalyzer;

/**
 * Class FieldType
 * @author Beno!t POLASZEK
 */
class FieldType implements StringableInterface, \ArrayAccess, FieldTypeInterface {

    protected $name;
    protected $class;
    protected $positionIncrementGap;
    protected $autoGeneratePhraseQueries;
    protected $docValuesFormat;
    protected $postingsFormat;
    protected $indexed;
    protected $stored;
    protected $docValues;
    protected $sortMissingFirst;
    protected $sortMissingLast;
    protected $multiValued;
    protected $omitNorms;
    protected $omitTermFreqAndPositions;
    protected $omitPositions;
    protected $termVectors;
    protected $termPositions;
    protected $termOffsets;
    protected $termPayloads;
    protected $analyzers = array();

    public function __construct($name = null, $class = null) {
        $this->name = $name;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this - Provides Fluent Interface
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
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
     * @return int
     */
    public function getPositionIncrementGap() {
        return $this->positionIncrementGap;
    }

    /**
     * @param int $positionIncrementGap
     * @return $this - Provides Fluent Interface
     */
    public function setPositionIncrementGap($positionIncrementGap) {
        $this->positionIncrementGap = $positionIncrementGap;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAutoGeneratePhraseQueries() {
        return $this->autoGeneratePhraseQueries;
    }

    /**
     * @param boolean $autoGeneratePhraseQueries
     * @return $this - Provides Fluent Interface
     */
    public function setAutoGeneratePhraseQueries($autoGeneratePhraseQueries) {
        $this->autoGeneratePhraseQueries = (bool) $autoGeneratePhraseQueries;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocValuesFormat() {
        return $this->docValuesFormat;
    }

    /**
     * @param string $docValuesFormat
     * @return $this - Provides Fluent Interface
     */
    public function setDocValuesFormat($docValuesFormat) {
        $this->docValuesFormat = $docValuesFormat;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostingsFormat() {
        return $this->postingsFormat;
    }

    /**
     * @param string $postingsFormat
     * @return $this - Provides Fluent Interface
     */
    public function setPostingsFormat($postingsFormat) {
        $this->postingsFormat = $postingsFormat;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isIndexed() {
        return $this->indexed;
    }

    /**
     * @param boolean $indexed
     * @return $this - Provides Fluent Interface
     */
    public function setIndexed($indexed) {
        $this->indexed = (bool) $indexed;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isStored() {
        return $this->stored;
    }

    /**
     * @param boolean $stored
     * @return $this - Provides Fluent Interface
     */
    public function setStored($stored) {
        $this->stored = (bool) $stored;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDocValues() {
        return $this->docValues;
    }

    /**
     * @param boolean $docValues
     * @return $this - Provides Fluent Interface
     */
    public function setDocValues($docValues) {
        $this->docValues = (bool) $docValues;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortMissingFirst() {
        return $this->sortMissingFirst;
    }

    /**
     * @param boolean $sortMissingFirst
     * @return $this - Provides Fluent Interface
     */
    public function setSortMissingFirst($sortMissingFirst) {
        $this->sortMissingFirst = (bool) $sortMissingFirst;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortMissingLast() {
        return $this->sortMissingLast;
    }

    /**
     * @param boolean $sortMissingLast
     * @return $this - Provides Fluent Interface
     */
    public function setSortMissingLast($sortMissingLast) {
        $this->sortMissingLast = (bool) $sortMissingLast;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMultiValued() {
        return $this->multiValued;
    }

    /**
     * @param boolean $multiValued
     * @return $this - Provides Fluent Interface
     */
    public function setMultiValued($multiValued) {
        $this->multiValued = (bool) $multiValued;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOmitNorms() {
        return $this->omitNorms;
    }

    /**
     * @param boolean $omitNorms
     * @return $this - Provides Fluent Interface
     */
    public function setOmitNorms($omitNorms) {
        $this->omitNorms = (bool) $omitNorms;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOmitTermFreqAndPositions() {
        return $this->omitTermFreqAndPositions;
    }

    /**
     * @param boolean $omitTermFreqAndPositions
     * @return $this - Provides Fluent Interface
     */
    public function setOmitTermFreqAndPositions($omitTermFreqAndPositions) {
        $this->omitTermFreqAndPositions = (bool) $omitTermFreqAndPositions;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isOmitPositions() {
        return $this->omitPositions;
    }

    /**
     * @param boolean $omitPositions
     * @return $this - Provides Fluent Interface
     */
    public function setOmitPositions($omitPositions) {
        $this->omitPositions = (bool) $omitPositions;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTermVectors() {
        return $this->termVectors;
    }

    /**
     * @param boolean $termVectors
     * @return $this - Provides Fluent Interface
     */
    public function setTermVectors($termVectors) {
        $this->termVectors = (bool) $termVectors;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTermPositions() {
        return $this->termPositions;
    }

    /**
     * @param boolean $termPositions
     * @return $this - Provides Fluent Interface
     */
    public function setTermPositions($termPositions) {
        $this->termPositions = (bool) $termPositions;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTermOffsets() {
        return $this->termOffsets;
    }

    /**
     * @param boolean $termOffsets
     * @return $this - Provides Fluent Interface
     */
    public function setTermOffsets($termOffsets) {
        $this->termOffsets = (bool) $termOffsets;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTermPayloads() {
        return $this->termPayloads;
    }

    /**
     * @param boolean $termPayloads
     * @return $this - Provides Fluent Interface
     */
    public function setTermPayloads($termPayloads) {
        $this->termPayloads = (bool) $termPayloads;
        return $this;
    }

    /**
     * @return AnalyzerInterface[]
     */
    public function getAnalyzers() {
        return $this->analyzers;
    }

    /**
     * @param AnalyzerInterface[] $analyzers
     * @return $this - Provides Fluent Interface
     */
    public function setAnalyzers(array $analyzers) {
        $this->analyzers = array();
        foreach ($analyzers AS $analyzer)
            $this->addAnalyzer($analyzer);
        return $this;
    }

    /**
     * @param AnalyzerInterface $analyzer
     * @return $this
     */
    public function addAnalyzer(AnalyzerInterface $analyzer) {
        $this->analyzers[] = $analyzer;
        return $this;
    }

    /**
     * @param null $type
     * @return AnalyzerInterface|StandardAnalyzer|IndexAnalyzer|QueryAnalyzer
     */
    public function createAnalyzer($type = null) {
        switch ($type) {
            case 'index' :
                $analyzer = new IndexAnalyzer();
                break;
            case 'query':
                $analyzer = new QueryAnalyzer();
                break;
            case null:
                $analyzer = new StandardAnalyzer();
                break;
            default:
                throw new OutOfBoundsException("Invalid analyzer type");
        }
        $this->addAnalyzer($analyzer);
        return $analyzer;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function castAsArray() {

        $out = array();

        $attributes = array(
            'name',
            'class',
            'positionIncrementGap',
            'autoGeneratePhraseQueries',
            'docValuesFormat',
            'postingsFormat',
            'indexed',
            'stored',
            'docValues',
            'sortMissingFirst',
            'sortMissingLast',
            'multiValued',
            'omitNorms',
            'omitTermFreqAndPositions',
            'omitPositions',
            'termVectors',
            'termPositions',
            'termOffsets',
            'termPayloads',
        );

        foreach ($attributes AS $attribute)
            if (!is_null($this[$attribute]))
                $out[$attribute] = $this[$attribute];

        if ($this->getAnalyzers()) {
            $out['analyzers'] = array_map(function (AnalyzerInterface $analyzer) {
                return $analyzer->castAsArray();
            }, $this->getAnalyzers());
        }

        return $out;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * @see \ArrayAccess::offsetExists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return method_exists($this, 'get' . $offset) || method_exists($this, 'is' . $offset) || (stripos($offset, 'is') === 0 && method_exists($this, $offset));
    }

    /**
     * @see \ArrayAccess::offsetGet
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset) {
        if (method_exists($this, 'get' . $offset))
            return call_user_func([$this, 'get' . $offset]);

        elseif (method_exists($this, 'is' . $offset))
            return call_user_func([$this, 'is' . $offset]);

        elseif (stripos($offset, 'is') === 0 && method_exists($this, $offset))
            return call_user_func([$this, $offset]);

        else
            return null;
    }

    /**
     * @see \ArrayAccess::offsetSet
     * @param mixed $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value) {
        return method_exists($this, 'set' . $offset) ? call_user_func([$this, 'set' . $offset], $value) : $this;
    }

    /**
     * @see \ArrayAccess::offsetUnset
     * @param mixed $offset
     * @return $this
     */
    public function offsetUnset($offset) {
        return $this->offsetSet($offset, null);
    }

}