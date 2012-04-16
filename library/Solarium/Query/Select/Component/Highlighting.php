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
 * Highlighting component
 *
 * @link http://wiki.apache.org/solr/HighlightingParameters
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Component_Highlighting extends Solarium_Query_Select_Component
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
     * Component type
     *
     * @var string
     */
    protected $_type = Solarium_Query_Select::COMPONENT_HIGHLIGHTING;

    /**
     * Array of fields for highlighting
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Initialize options
     *
     * The field option needs setup work
     *
     * @return void
     */
    protected function _init()
    {
        foreach ($this->_options AS $name => $value) {
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
     * @param string $name
     * @param boolean $autocreate
     * @return Solarium_Query_Select_Component_Highlighting_Field
     */
    public function getField($name, $autocreate = true)
    {
        if (isset($this->_fields[$name])) {
            return $this->_fields[$name];
        } else if ($autocreate) {
            $this->addField($name);
            return $this->_fields[$name];
        } else {
            return null;
        }
    }

    /**
     * Add a field for highlighting
     *
     * @param string|array|Solarium_Query_Select_Component_Highlighting_Field $field
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function addField($field)
    {
        // autocreate object for string input
        if (is_string($field)) {
            $field = new Solarium_Query_Select_Component_Highlighting_Field(array('name' => $field));
        } else if (is_array($field)) {
            $field = new Solarium_Query_Select_Component_Highlighting_Field($field);
        }

        // validate field
        if ($field->getName() === null) {
            throw new Solarium_Exception('To add a highlighting field it needs to have at least a "name" setting');
        }

        $this->_fields[$field->getName()] = $field;
        return $this;
    }

    /**
     * Add multiple fields for highlighting
     *
     * @param string|array $fields can be an array of object instances or a string with comma
     * separated fieldnames
     *
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function addFields($fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        foreach ($fields AS $key => $field) {

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
     * @param string $field
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function removeField($field)
    {
        if (isset($this->_fields[$field])) {
           unset($this->_fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields
     *
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function clearFields()
    {
        $this->_fields = array();
        return $this;
    }

    /**
     * Get the list of fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Set multiple fields
     *
     * This overwrites any existing fields
     *
     * @param array $fields
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
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
     * @param int $maximum
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setSnippets($maximum)
    {
        return $this->_setOption('snippets', $maximum);
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
     * @param int $size
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setFragSize($size)
    {
        return $this->_setOption('fragsize', $size);
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
     * @param boolean $merge
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setMergeContiguous($merge)
    {
        return $this->_setOption('mergecontiguous', $merge);
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
     * @param boolean $require
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setRequireFieldMatch($require)
    {
        return $this->_setOption('requirefieldmatch', $require);
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
     * @param int $chars
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setMaxAnalyzedChars($chars)
    {
        return $this->_setOption('maxanalyzedchars', $chars);
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
     * @param string $field
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setAlternateField($field)
    {
        return $this->_setOption('alternatefield', $field);
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
     * @param int $length
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setMaxAlternateFieldLength($length)
    {
        return $this->_setOption('maxalternatefieldlength', $length);
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
     * @param string $formatter
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setFormatter($formatter = 'simple')
    {
        return $this->_setOption('formatter', $formatter);
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
     * @param string $prefix
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setSimplePrefix($prefix)
    {
        return $this->_setOption('simpleprefix', $prefix);
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
     * @param string $postfix
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setSimplePostfix($postfix)
    {
        return $this->_setOption('simplepostfix', $postfix);
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
     * Set fragmenter option
     *
     * Use one of the constants as value.
     *
     * @param string $fragmenter
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setFragmenter($fragmenter)
    {
        return $this->_setOption('fragmenter', $fragmenter);
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
     * @param string $builder
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setFragListBuilder($builder)
    {
        return $this->_setOption('fraglistbuilder', $builder);
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
     * @param string $builder
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setFragmentsBuilder($builder)
    {
        return $this->_setOption('fragmentsbuilder', $builder);
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
     * @param boolean $use
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setUseFastVectorHighlighter($use)
    {
        return $this->_setOption('usefastvectorhighlighter', $use);
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
     * @param boolean $use
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setUsePhraseHighlighter($use)
    {
        return $this->_setOption('usephrasehighlighter', $use);
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
     * @param boolean $highlight
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setHighlightMultiTerm($highlight)
    {
        return $this->_setOption('highlightmultiterm', $highlight);
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
     * @param float $slop
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setRegexSlop($slop)
    {
        return $this->_setOption('regexslop', $slop);
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
     * @param string $pattern
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setRegexPattern($pattern)
    {
        return $this->_setOption('regexpattern', $pattern);
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
     * @param int $chars
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setRegexMaxAnalyzedChars($chars)
    {
        return $this->_setOption('regexmaxanalyzedchars', $chars);
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
     * @param string $query
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setQuery($query)
    {
        return $this->_setOption('query', $query);
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
     * @param int $maximum
     * @return Solarium_Query_Select_Component_Highlighting Provides fluent interface
     */
    public function setPhraseLimit($maximum)
    {
        return $this->_setOption('phraselimit', $maximum);
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

}