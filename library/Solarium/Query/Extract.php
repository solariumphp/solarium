<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 * Copyright 2012 Alexander Brausewetter. All rights reserved.
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
 * @copyright Copyright 2012 Alexander Brausewetter <alex@helpdeskhq.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Query
 */

/**
 * Extract query
 *
 * Sends a document extract request to Solr, i.e. upload rich document content 
 * such as PDF, Word or HTML, parse the file contents and add it to the index. 
 *
 * The Solr server must have the {@link http://wiki.apache.org/solr/ExtractingRequestHandler 
 * ExtractingRequestHandler} enabled.
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Extract extends Solarium_Query
{
    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'handler'     => 'update/extract',
        'resultclass' => 'Solarium_Result_Update',
    );

    /**
     * Field name mappings
     *
     * @var array
     */
    protected $_fieldMappings = array();

    /**
     * Get type for this query
     *
     * @return string
     */
    public function getType()
    {
        return Solarium_Client::QUERYTYPE_EXTRACT;
    }
    
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
        if (isset($this->_options['fmap'])) {
            $this->setFieldMappings($this->_options['fmap']);
        }
    }

    // {{{ Options

    /**
     * Set the document with literal fields and boost settings
     *
     * The fields in the document are indexed together with the generated 
     * fields that Solr extracts from the file.
     * 
     * @param Solarium_Document_ReadWrite $document
     * @return Solarium_Query_Extract
     */
    public function setDocument($document)
    {
        return $this->_setOption('document', $document);
    }

    /**
     * Get the document with literal fields and boost settings
     * 
     * @return Solarium_Document_ReadWrite|null
     */
    public function getDocument()
    {
        return $this->getOption('document');
    }

    /**
     * Set the file to upload and index
     * 
     * @param string $filename
     * @return Solarium_Query_Extract
     */
    public function setFile($filename)
    {
        return $this->_setOption('file', $filename);
    }

    /**
     * Get the file to upload and index
     * 
     * @return string|null
     */
    public function getFile()
    {
        return $this->getOption('file');
    }

    /**
     * Set the prefix for fields that are not defined in the schema
     *
     * @param string $uprefix
     * @return Solarium_Query_Extract
     */
    public function setUprefix($uprefix)
    {
        return $this->_setOption('uprefix', $uprefix);
    }

    /**
     * Get the prefix for fields that are not defined in the schema
     * 
     * @return string|null
     */
    public function getUprefix()
    {
        return $this->getOption('uprefix');
    }

    /**
     * Set the field to use if uprefix is not specified and a field cannot be 
     * determined
     *
     * @param string $defaultField
     * @return Solarium_Query_Extract
     */
    public function setDefaultField($defaultField)
    {
        return $this->_setOption('defaultField', $defaultField);
    }

    /**
     * Get the field to use if uprefix is not specified and a field cannot be 
     * determined
     * 
     * @return string|null
     */
    public function getDefaultField()
    {
        return $this->getOption('defaultField');
    }

    /**
     * Set if all field names should be mapped to lowercase with underscores. 
     * For example, Content-Type would be mapped to content_type.
     *
     * @param bool $lowerNames
     * @return Solarium_Query_Extract
     */
    public function setLowernames($lowerNames)
    {
        return $this->_setOption('lowernames', (bool) $lowerNames);
    }

    /**
     * Get if all field names should be mapped to lowercase with underscores
     * 
     * @return bool
     */
    public function getLowernames()
    {
        return $this->getOption('lowernames');
    }

    /**
     * Set if the extract should be committed immediately
     *
     * @param bool $commit
     * @return Solarium_Query_Extract Provides fluent interface
     */
    public function setCommit($commit)
    {
        return $this->_setOption('commit', (bool) $commit);
    }

    /**
     * Get if the extract should be committed immediately
     * 
     * @return bool
     */
    public function getCommit()
    {
        return $this->getOption('commit');
    }

    /**
     * Set milliseconds until extract update is committed. Since Solr 3.4
     *
     * @param int $commitWithin
     * @return Solarium_Query_Extract Provides fluent interface
     */
    public function setCommitWithin($commitWithin)
    {
        return $this->_setOption('commitWithin', $commitWithin);
    }

    /**
     * Get milliseconds until extract update is committed. Since Solr 3.4
     * 
     * @return int
     */
    public function getCommitWithin()
    {
        return $this->getOption('commitWithin');
    }

    // }}}

    // {{{ Field Mappings

    /**
     * Add a name mapping from one field to another
     *
     * Example: fmap.content=text will cause the content field normally 
     * generated by Tika to be moved to the "text" field.
     *
     * @param string      $fromField Original field name
     * @param mixed|array $toField   New field name
     * @return Solarium_Query_Extract Provides fluent interface
     */
    public function addFieldMapping($fromField, $toField)
    {
        $this->_fieldMappings[$fromField] = $toField;

        return $this;
    }

    /**
     * Add multiple field name mappings
     *
     * @param array $mappings Name mapping in the form [$fromField => $toField, ...]
     * @return Solarium_Query_Extract Provides fluent interface
     */
    public function addFieldMappings($mappings)
    {
        foreach ($mappings AS $fromField => $toField) {
            $this->addFieldMapping($fromField, $toField);
        }

        return $this;
    }

    /**
     * Remove a field name mapping
     *
     * @param string $fromField
     * @return Solarium_Query_Extract Provides fluent interface
     */
    public function removeFieldMapping($fromField)
    {
        if (isset($this->_fieldMappings[$fromField])) {
            unset($this->_fieldMappings[$fromField]);
        }

        return $this;
    }

    /**
     * Remove all field name mappings
     *
     * @return Solarium_Query_Extract Provides fluent interface
     */
    public function clearFieldMappings()
    {
        $this->_fieldMappings = array();
        return $this;
    }

    /**
     * Get all field name mappings
     *
     * @return array
     */
    public function getFieldMappings()
    {
        return $this->_fieldMappings;
    }

    /**
     * Set many field name mappings. This overwrites any existing fields.
     *
     * @param array $mappings Name mapping in the form [$fromField => $toField, ...]
     * @return Solarium_Query_Extract Provides fluent interface
     */
    public function setFieldMappings($mappings)
    {
        $this->clearFieldMappings();
        $this->addFieldMappings($mappings);

        return $this;
    }

    // }}}
}
