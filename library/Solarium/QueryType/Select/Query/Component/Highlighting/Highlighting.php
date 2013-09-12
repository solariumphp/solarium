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
namespace Solarium\QueryType\Select\Query\Component\Highlighting;

use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Query\Component\Component;
use Solarium\QueryType\Select\RequestBuilder\Component\Highlighting as RequestBuilder;
use Solarium\QueryType\Select\ResponseParser\Component\Highlighting as ResponseParser;
use Solarium\Exception\InvalidArgumentException;

/**
 * Highlighting component
 *
 * @link http://wiki.apache.org/solr/HighlightingParameters
 */
class Highlighting extends Component
{
    /**
     * Value for fragmenter option gap
     */
    const FRAGMENTER_GAP = 'gap';

    /**
     * Value for fragmenter option regex
     */
    const FRAGMENTER_REGEX = 'regex';

    /**
     * Value for BoundaryScanner type
     */
    const BOUNDARYSCANNER_TYPE_CHARACTER = 'CHARACTER';

    /**
     * Value for BoundaryScanner type
     */
    const BOUNDARYSCANNER_TYPE_WORD = 'WORD';

    /**
     * Value for BoundaryScanner type
     */
    const BOUNDARYSCANNER_TYPE_SENTENCE = 'SENTENCE';

    /**
     * Value for BoundaryScanner type
     */
    const BOUNDARYSCANNER_TYPE_LINE = 'LINE';

    /**
     * Array of fields for highlighting
     *
     * @var array
     */
    protected $fields = array();

    /**
     * Get component type
     *
     * @return string
     */
    public function getType()
    {
        return SelectQuery::COMPONENT_HIGHLIGHTING;
    }

    /**
     * Get a requestbuilder for this query
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder;
    }

    /**
     * Get a response parser for this query
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser;
    }

    /**
     * Initialize options
     *
     * The field option needs setup work
     *
     * @return void
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'field':
                    $this->addFields($value);
                    break;
            }
        }
    }

    /**
     * Get a field options object
     *
     * @param  string  $name
     * @param  boolean $autocreate
     * @return Field
     */
    public function getField($name, $autocreate = true)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } elseif ($autocreate) {
            $this->addField($name);

            return $this->fields[$name];
        } else {
            return null;
        }
    }

    /**
     * Add a field for highlighting
     *
     * @throws InvalidArgumentException
     * @param  string|array|Field       $field
     * @return self                     Provides fluent interface
     */
    public function addField($field)
    {
        // autocreate object for string input
        if (is_string($field)) {
            $field = new Field(array('name' => $field));
        } elseif (is_array($field)) {
            $field = new Field($field);
        }

        // validate field
        if ($field->getName() === null) {
            throw new InvalidArgumentException(
                'To add a highlighting field it needs to have at least a "name" setting'
            );
        }

        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * Add multiple fields for highlighting
     *
     * @param string|array $fields can be an array of object instances or a string with comma
     * separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function addFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        foreach ($fields as $key => $field) {

            // in case of a config array without key: add key to config
            if (is_array($field) && !isset($field['name'])) {
                $field['name'] = $key;
            }

            $this->addField($field);
        }

        return $this;
    }

    /**
     * Remove a highlighting field
     *
     * @param  string $field
     * @return self   Provides fluent interface
     */
    public function removeField($field)
    {
        if (isset($this->fields[$field])) {
            unset($this->fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields
     *
     * @return self Provides fluent interface
     */
    public function clearFields()
    {
        $this->fields = array();

        return $this;
    }

    /**
     * Get the list of fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set multiple fields
     *
     * This overwrites any existing fields
     *
     * @param  array $fields
     * @return self  Provides fluent interface
     */
    public function setFields($fields)
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Set snippets option
     *
     * Maximum number of snippets per field
     *
     * @param  int  $maximum
     * @return self Provides fluent interface
     */
    public function setSnippets($maximum)
    {
        return $this->setOption('snippets', $maximum);
    }

    /**
     * Get snippets option
     *
     * @return int|null
     */
    public function getSnippets()
    {
        return $this->getOption('snippets');
    }

    /**
     * Set fragsize option
     *
     * The size, in characters, of fragments to consider for highlighting
     *
     * @param  int  $size
     * @return self Provides fluent interface
     */
    public function setFragSize($size)
    {
        return $this->setOption('fragsize', $size);
    }

    /**
     * Get fragsize option
     *
     * @return int|null
     */
    public function getFragSize()
    {
        return $this->getOption('fragsize');
    }

    /**
     * Set mergeContiguous option
     *
     * Collapse contiguous fragments into a single fragment
     *
     * @param  boolean $merge
     * @return self    Provides fluent interface
     */
    public function setMergeContiguous($merge)
    {
        return $this->setOption('mergecontiguous', $merge);
    }

    /**
     * Get mergeContiguous option
     *
     * @return boolean|null
     */
    public function getMergeContiguous()
    {
        return $this->getOption('mergecontiguous');
    }

    /**
     * Set requireFieldMatch option
     *
     * @param  boolean $require
     * @return self    Provides fluent interface
     */
    public function setRequireFieldMatch($require)
    {
        return $this->setOption('requirefieldmatch', $require);
    }

    /**
     * Get requireFieldMatch option
     *
     * @return boolean|null
     */
    public function getRequireFieldMatch()
    {
        return $this->getOption('requirefieldmatch');
    }

    /**
     * Set maxAnalyzedChars option
     *
     * How many characters into a document to look for suitable snippets
     *
     * @param  int  $chars
     * @return self Provides fluent interface
     */
    public function setMaxAnalyzedChars($chars)
    {
        return $this->setOption('maxanalyzedchars', $chars);
    }

    /**
     * Get maxAnalyzedChars option
     *
     * @return int|null
     */
    public function getMaxAnalyzedChars()
    {
        return $this->getOption('maxanalyzedchars');
    }

    /**
     * Set alternatefield option
     *
     * @param  string $field
     * @return self   Provides fluent interface
     */
    public function setAlternateField($field)
    {
        return $this->setOption('alternatefield', $field);
    }

    /**
     * Get alternatefield option
     *
     * @return string|null
     */
    public function getAlternateField()
    {
        return $this->getOption('alternatefield');
    }

    /**
     * Set maxAlternateFieldLength option
     *
     * @param  int  $length
     * @return self Provides fluent interface
     */
    public function setMaxAlternateFieldLength($length)
    {
        return $this->setOption('maxalternatefieldlength', $length);
    }

    /**
     * Get maxAlternateFieldLength option
     *
     * @return int|null
     */
    public function getMaxAlternateFieldLength()
    {
        return $this->getOption('maxalternatefieldlength');
    }

    /**
     * Set formatter option
     *
     * @param  string $formatter
     * @return self   Provides fluent interface
     */
    public function setFormatter($formatter = 'simple')
    {
        return $this->setOption('formatter', $formatter);
    }

    /**
     * Get formatter option
     *
     * @return string|null
     */
    public function getFormatter()
    {
        return $this->getOption('formatter');
    }

    /**
     * Set simple prefix option
     *
     * Solr option h1.simple.pre
     *
     * @param  string $prefix
     * @return self   Provides fluent interface
     */
    public function setSimplePrefix($prefix)
    {
        return $this->setOption('simpleprefix', $prefix);
    }

    /**
     * Get simple prefix option
     *
     * Solr option hl.simple.pre
     *
     * @return string|null
     */
    public function getSimplePrefix()
    {
        return $this->getOption('simpleprefix');
    }

    /**
     * Set simple postfix option
     *
     * Solr option h1.simple.post
     *
     * @param  string $postfix
     * @return self   Provides fluent interface
     */
    public function setSimplePostfix($postfix)
    {
        return $this->setOption('simplepostfix', $postfix);
    }

    /**
     * Get simple postfix option
     *
     * Solr option hl.simple.post
     *
     * @return string|null
     */
    public function getSimplePostfix()
    {
        return $this->getOption('simplepostfix');
    }

    /**
     * Set tag prefix option
     *
     * Solr option h1.tag.post
     *
     * @param  string $prefix
     * @return self   Provides fluent interface
     */
    public function setTagPrefix($prefix)
    {
        return $this->setOption('tagprefix', $prefix);
    }

    /**
     * Get tag prefix option
     *
     * Solr option hl.tag.pre
     *
     * @return string|null
     */
    public function getTagPrefix()
    {
        return $this->getOption('tagprefix');
    }

    /**
     * Set tag postfix option
     *
     * Solr option h1.tag.post
     *
     * @param  string $postfix
     * @return self   Provides fluent interface
     */
    public function setTagPostfix($postfix)
    {
        return $this->setOption('tagpostfix', $postfix);
    }

    /**
     * Get tag postfix option
     *
     * Solr option hl.tag.post
     *
     * @return string|null
     */
    public function getTagPostfix()
    {
        return $this->getOption('tagpostfix');
    }

    /**
     * Set fragmenter option
     *
     * Use one of the constants as value.
     *
     * @param  string $fragmenter
     * @return self   Provides fluent interface
     */
    public function setFragmenter($fragmenter)
    {
        return $this->setOption('fragmenter', $fragmenter);
    }

    /**
     * Get fragmenter option
     *
     * @return string|null
     */
    public function getFragmenter()
    {
        return $this->getOption('fragmenter');
    }

    /**
     * Set fraglistbuilder option
     *
     * @param  string $builder
     * @return self   Provides fluent interface
     */
    public function setFragListBuilder($builder)
    {
        return $this->setOption('fraglistbuilder', $builder);
    }

    /**
     * Get fraglistbuilder option
     *
     * @return string|null
     */
    public function getFragListBuilder()
    {
        return $this->getOption('fraglistbuilder');
    }

    /**
     * Set fragmentsbuilder option
     *
     * @param  string $builder
     * @return self   Provides fluent interface
     */
    public function setFragmentsBuilder($builder)
    {
        return $this->setOption('fragmentsbuilder', $builder);
    }

    /**
     * Get fragmentsbuilder option
     *
     * @return string|null
     */
    public function getFragmentsBuilder()
    {
        return $this->getOption('fragmentsbuilder');
    }

    /**
     * Set useFastVectorHighlighter option
     *
     * @param  boolean $use
     * @return self    Provides fluent interface
     */
    public function setUseFastVectorHighlighter($use)
    {
        return $this->setOption('usefastvectorhighlighter', $use);
    }

    /**
     * Get useFastVectorHighlighter option
     *
     * @return boolean|null
     */
    public function getUseFastVectorHighlighter()
    {
        return $this->getOption('usefastvectorhighlighter');
    }

    /**
     * Set usePhraseHighlighter option
     *
     * @param  boolean $use
     * @return self    Provides fluent interface
     */
    public function setUsePhraseHighlighter($use)
    {
        return $this->setOption('usephrasehighlighter', $use);
    }

    /**
     * Get usePhraseHighlighter option
     *
     * @return boolean|null
     */
    public function getUsePhraseHighlighter()
    {
        return $this->getOption('usephrasehighlighter');
    }

    /**
     * Set HighlightMultiTerm option
     *
     * @param  boolean $highlight
     * @return self    Provides fluent interface
     */
    public function setHighlightMultiTerm($highlight)
    {
        return $this->setOption('highlightmultiterm', $highlight);
    }

    /**
     * Get HighlightMultiTerm option
     *
     * @return boolean|null
     */
    public function getHighlightMultiTerm()
    {
        return $this->getOption('highlightmultiterm');
    }

    /**
     * Set RegexSlop option
     *
     * @param  float $slop
     * @return self  Provides fluent interface
     */
    public function setRegexSlop($slop)
    {
        return $this->setOption('regexslop', $slop);
    }

    /**
     * Get RegexSlop option
     *
     * @return float|null
     */
    public function getRegexSlop()
    {
        return $this->getOption('regexslop');
    }

    /**
     * Set RegexPattern option
     *
     * @param  string $pattern
     * @return self   Provides fluent interface
     */
    public function setRegexPattern($pattern)
    {
        return $this->setOption('regexpattern', $pattern);
    }

    /**
     * Get RegexPattern option
     *
     * @return string|null
     */
    public function getRegexPattern()
    {
        return $this->getOption('regexpattern');
    }

    /**
     * Set RegexMaxAnalyzedChars option
     *
     * @param  int  $chars
     * @return self Provides fluent interface
     */
    public function setRegexMaxAnalyzedChars($chars)
    {
        return $this->setOption('regexmaxanalyzedchars', $chars);
    }

    /**
     * Get RegexMaxAnalyzedChars option
     *
     * @return int|null
     */
    public function getRegexMaxAnalyzedChars()
    {
        return $this->getOption('regexmaxanalyzedchars');
    }

    /**
     * Set highlight query option
     *
     * Overrides the q parameter for highlighting
     *
     * @param  string $query
     * @return self   Provides fluent interface
     */
    public function setQuery($query)
    {
        return $this->setOption('query', $query);
    }

    /**
     * Get query option
     *
     * @return string|null
     */
    public function getQuery()
    {
        return $this->getOption('query');
    }

    /**
     * Set phraselimit option
     *
     * @param  int  $maximum
     * @return self Provides fluent interface
     */
    public function setPhraseLimit($maximum)
    {
        return $this->setOption('phraselimit', $maximum);
    }

    /**
     * Get phraselimit option
     *
     * @return int|null
     */
    public function getPhraseLimit()
    {
        return $this->getOption('phraselimit');
    }

    /**
     * Set MultiValuedSeparatorChar option
     *
     * @param  string $separator
     * @return self   Provides fluent interface
     */
    public function setMultiValuedSeparatorChar($separator)
    {
        return $this->setOption('multivaluedseparatorchar', $separator);
    }

    /**
     * Get MultiValuedSeparatorChar option
     *
     * @return string
     */
    public function getMultiValuedSeparatorChar()
    {
        return $this->getOption('multivaluedseparatorchar');
    }

    /**
     * Set boundaryscannermaxscan option
     *
     * @param  int  $maximum
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerMaxScan($maximum)
    {
        return $this->setOption('boundaryscannermaxscan', $maximum);
    }

    /**
     * Get boundaryscannermaxscan option
     *
     * @return int|null
     */
    public function getBoundaryScannerMaxScan()
    {
        return $this->getOption('boundaryscannermaxscan');
    }

    /**
     * Set boundaryscannerchars option
     *
     * @param  string  $chars
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerChars($chars)
    {
        return $this->setOption('boundaryscannerchars', $chars);
    }

    /**
     * Get boundaryscannerchars option
     *
     * @return string|null
     */
    public function getBoundaryScannerChars()
    {
        return $this->getOption('boundaryscannerchars');
    }

    /**
     * Set boundaryscannertype option
     *
     * @param  string  $type
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerType($type)
    {
        return $this->setOption('boundaryscannertype', $type);
    }

    /**
     * Get boundaryscannertype option
     *
     * @return string|null
     */
    public function getBoundaryScannerType()
    {
        return $this->getOption('boundaryscannertype');
    }

    /**
     * Set boundaryscannerlanguage option
     *
     * @param  string  $language
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerLanguage($language)
    {
        return $this->setOption('boundaryscannerlanguage', $language);
    }

    /**
     * Get boundaryscannerlanguage option
     *
     * @return string|null
     */
    public function getBoundaryScannerLanguage()
    {
        return $this->getOption('boundaryscannerlanguage');
    }

    /**
     * Set boundaryscannercountry option
     *
     * @param  string  $country
     * @return self Provides fluent interface
     */
    public function setBoundaryScannerCountry($country)
    {
        return $this->setOption('boundaryscannercountry', $country);
    }

    /**
     * Get boundaryscannercountry option
     *
     * @return string|null
     */
    public function getBoundaryScannerCountry()
    {
        return $this->getOption('boundaryscannercountry');
    }
}
