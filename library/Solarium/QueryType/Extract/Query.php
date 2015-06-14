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
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Extract;

use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Update\ResponseParser as UpdateResponseParser;
use Solarium\QueryType\Update\Query\Document\DocumentInterface;

/**
 * Extract query.
 *
 * Sends a document extract request to Solr, i.e. upload rich document content
 * such as PDF, Word or HTML, parse the file contents and add it to the index.
 *
 * The Solr server must have the {@link http://wiki.apache.org/solr/ExtractingRequestHandler
 * ExtractingRequestHandler} enabled.
 */
class Query extends BaseQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = array(
        'handler'     => 'update/extract',
        'resultclass' => 'Solarium\QueryType\Extract\Result',
        'documentclass' => 'Solarium\QueryType\Update\Query\Document\Document',
        'omitheader'  => true,
        'extractonly' => false,
    );

    /**
     * Field name mappings.
     *
     * @var array
     */
    protected $fieldMappings = array();

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_EXTRACT;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return UpdateResponseParser
     */
    public function getResponseParser()
    {
        return new UpdateResponseParser();
    }

    /**
     * Set the document with literal fields and boost settings.
     *
     * The fields in the document are indexed together with the generated
     * fields that Solr extracts from the file.
     *
     * @param DocumentInterface $document
     *
     * @return self
     */
    public function setDocument(DocumentInterface $document)
    {
        return $this->setOption('document', $document);
    }

    /**
     * Get the document with literal fields and boost settings.
     *
     * @return DocumentInterface|null
     */
    public function getDocument()
    {
        return $this->getOption('document');
    }

    /**
     * Set the file to upload and index.
     *
     * @param string $filename
     *
     * @return self
     */
    public function setFile($filename)
    {
        return $this->setOption('file', $filename);
    }

    /**
     * Get the file to upload and index.
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->getOption('file');
    }

    /**
     * Set the prefix for fields that are not defined in the schema.
     *
     * @param string $uprefix
     *
     * @return self
     */
    public function setUprefix($uprefix)
    {
        return $this->setOption('uprefix', $uprefix);
    }

    /**
     * Get the prefix for fields that are not defined in the schema.
     *
     * @return string|null
     */
    public function getUprefix()
    {
        return $this->getOption('uprefix');
    }

    /**
     * Set the field to use if uprefix is not specified and a field cannot be
     * determined.
     *
     * @param string $defaultField
     *
     * @return self
     */
    public function setDefaultField($defaultField)
    {
        return $this->setOption('defaultField', $defaultField);
    }

    /**
     * Get the field to use if uprefix is not specified and a field cannot be
     * determined.
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
     *
     * @return self
     */
    public function setLowernames($lowerNames)
    {
        return $this->setOption('lowernames', (bool) $lowerNames);
    }

    /**
     * Get if all field names should be mapped to lowercase with underscores.
     *
     * @return bool
     */
    public function getLowernames()
    {
        return $this->getOption('lowernames');
    }

    /**
     * Set if the extract should be committed immediately.
     *
     * @param bool $commit
     *
     * @return self Provides fluent interface
     */
    public function setCommit($commit)
    {
        return $this->setOption('commit', (bool) $commit);
    }

    /**
     * Get if the extract should be committed immediately.
     *
     * @return bool
     */
    public function getCommit()
    {
        return $this->getOption('commit');
    }

    /**
     * Set milliseconds until extract update is committed. Since Solr 3.4.
     *
     * @param int $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function setCommitWithin($commitWithin)
    {
        return $this->setOption('commitWithin', $commitWithin);
    }

    /**
     * Get milliseconds until extract update is committed. Since Solr 3.4.
     *
     * @return int
     */
    public function getCommitWithin()
    {
        return $this->getOption('commitWithin');
    }

    /**
     * Add a name mapping from one field to another.
     *
     * Example: fmap.content=text will cause the content field normally
     * generated by Tika to be moved to the "text" field.
     *
     * @param string      $fromField Original field name
     * @param mixed|array $toField   New field name
     *
     * @return self Provides fluent interface
     */
    public function addFieldMapping($fromField, $toField)
    {
        $this->fieldMappings[$fromField] = $toField;

        return $this;
    }

    /**
     * Add multiple field name mappings.
     *
     * @param array $mappings Name mapping in the form [$fromField => $toField, ...]
     *
     * @return self Provides fluent interface
     */
    public function addFieldMappings($mappings)
    {
        foreach ($mappings as $fromField => $toField) {
            $this->addFieldMapping($fromField, $toField);
        }

        return $this;
    }

    /**
     * Remove a field name mapping.
     *
     * @param string $fromField
     *
     * @return self Provides fluent interface
     */
    public function removeFieldMapping($fromField)
    {
        if (isset($this->fieldMappings[$fromField])) {
            unset($this->fieldMappings[$fromField]);
        }

        return $this;
    }

    /**
     * Remove all field name mappings.
     *
     * @return self Provides fluent interface
     */
    public function clearFieldMappings()
    {
        $this->fieldMappings = array();

        return $this;
    }

    /**
     * Get all field name mappings.
     *
     * @return array
     */
    public function getFieldMappings()
    {
        return $this->fieldMappings;
    }

    /**
     * Set many field name mappings. This overwrites any existing fields.
     *
     * @param array $mappings Name mapping in the form [$fromField => $toField, ...]
     *
     * @return self Provides fluent interface
     */
    public function setFieldMappings($mappings)
    {
        $this->clearFieldMappings();
        $this->addFieldMappings($mappings);

        return $this;
    }

    /**
     * Set a custom document class for use in the createDocument method.
     *
     * This class should implement the document interface
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setDocumentClass($value)
    {
        return $this->setOption('documentclass', $value);
    }

    /**
     * Get the current documentclass option.
     *
     * The value is a classname, not an instance
     *
     * @return string
     */
    public function getDocumentClass()
    {
        return $this->getOption('documentclass');
    }

    /**
     * Set the ExtractOnly parameter of SOLR Extraction Handler.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setExtractOnly($value)
    {
        return $this->setOption('extractonly', (bool) $value);
    }

    /**
     * Get the ExtractOnly parameter of SOLR Extraction Handler.
     *
     * @return boolean
     */
    public function getExtractOnly()
    {
        return $this->getOption('extractonly');
    }

    /**
     * Create a document object instance.
     *
     * You can optionally directly supply the fields and boosts
     * to get a ready-made document instance for direct use in an add command
     *
     * @param array $fields
     * @param array $boosts
     *
     * @return DocumentInterface
     */
    public function createDocument($fields = array(), $boosts = array())
    {
        $class = $this->getDocumentClass();

        return new $class($fields, $boosts);
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        if (isset($this->options['fmap'])) {
            $this->setFieldMappings($this->options['fmap']);
        }
    }
}
