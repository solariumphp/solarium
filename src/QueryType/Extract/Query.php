<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Extract;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\ResponseParser as UpdateResponseParser;

/**
 * Extract query.
 *
 * Sends a document extract request to Solr, i.e. upload rich document content
 * such as PDF, Word or HTML, parse the file contents and add it to the index.
 *
 * The Solr server must have the {@link https://solr.apache.org/guide/uploading-data-with-solr-cell-using-apache-tika.html#configuring-the-extractingrequesthandler-in-solrconfig-xml
 * ExtractingRequestHandler} enabled.
 */
class Query extends BaseQuery
{
    /**
     * Extract format 'text'.
     */
    const EXTRACT_FORMAT_TEXT = 'text';

    /**
     * Extract format 'xml'.
     */
    const EXTRACT_FORMAT_XML = 'xml';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'update/extract',
        'resultclass' => Result::class,
        'documentclass' => Document::class,
        'omitheader' => true,
        'extractonly' => false,
    ];

    /**
     * Field name mappings.
     *
     * @var array
     */
    protected $fieldMappings = [];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_EXTRACT;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return UpdateResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
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
    public function setDocument(DocumentInterface $document): self
    {
        $this->setOption('document', $document);

        return $this;
    }

    /**
     * Get the document with literal fields and boost settings.
     *
     * @return DocumentInterface|null
     */
    public function getDocument(): ?DocumentInterface
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
    public function setFile(string $filename): self
    {
        $this->setOption('file', $filename);

        return $this;
    }

    /**
     * Get the file to upload and index.
     *
     * @return string|null
     */
    public function getFile(): ?string
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
    public function setUprefix(string $uprefix): self
    {
        $this->setOption('uprefix', $uprefix);

        return $this;
    }

    /**
     * Get the prefix for fields that are not defined in the schema.
     *
     * @return string|null
     */
    public function getUprefix(): ?string
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
    public function setDefaultField(string $defaultField): self
    {
        $this->setOption('defaultField', $defaultField);

        return $this;
    }

    /**
     * Get the field to use if uprefix is not specified and a field cannot be
     * determined.
     *
     * @return string|null
     */
    public function getDefaultField(): ?string
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
    public function setLowernames(bool $lowerNames): self
    {
        $this->setOption('lowernames', (bool) $lowerNames);

        return $this;
    }

    /**
     * Get if all field names should be mapped to lowercase with underscores.
     *
     * @return bool
     */
    public function getLowernames(): ?bool
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
    public function setCommit(bool $commit): self
    {
        $this->setOption('commit', (bool) $commit);

        return $this;
    }

    /**
     * Get if the extract should be committed immediately.
     *
     * @return bool|null
     */
    public function getCommit(): ?bool
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
    public function setCommitWithin(int $commitWithin): self
    {
        $this->setOption('commitWithin', $commitWithin);

        return $this;
    }

    /**
     * Get milliseconds until extract update is committed. Since Solr 3.4.
     *
     * @return int
     */
    public function getCommitWithin(): ?int
    {
        return $this->getOption('commitWithin');
    }

    /**
     * Add a name mapping from one field to another.
     *
     * Example: fmap.content=text will cause the content field normally
     * generated by Tika to be moved to the "text" field.
     *
     * @param string $fromField Original field name
     * @param string $toField   New field name
     *
     * @return self Provides fluent interface
     */
    public function addFieldMapping(string $fromField, string $toField): self
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
    public function addFieldMappings(array $mappings): self
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
    public function removeFieldMapping(string $fromField): self
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
    public function clearFieldMappings(): self
    {
        $this->fieldMappings = [];

        return $this;
    }

    /**
     * Get all field name mappings.
     *
     * @return array
     */
    public function getFieldMappings(): array
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
    public function setFieldMappings(array $mappings): self
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
    public function setDocumentClass(string $value): self
    {
        $this->setOption('documentclass', $value);

        return $this;
    }

    /**
     * Get the current documentclass option.
     *
     * The value is a classname, not an instance
     *
     * @return string|null
     */
    public function getDocumentClass(): ?string
    {
        return $this->getOption('documentclass');
    }

    /**
     * Set the extractOnly parameter of the ExtractingRequestHandler.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setExtractOnly(bool $value): self
    {
        $this->setOption('extractonly', (bool) $value);

        return $this;
    }

    /**
     * Get the extractOnly parameter of the ExtractingRequestHandler.
     *
     * @return bool|null
     */
    public function getExtractOnly(): ?bool
    {
        return $this->getOption('extractonly');
    }

    /**
     * Set the extractFormat parameter of the ExtractingRequestHandler.
     *
     * This parameter is valid only if 'extractonly' is set to true.
     *
     * @param string $format Use one of the EXTRACT_FORMAT_* constants
     *
     * @return self Provides fluent interface
     *
     * @see setExtractOnly()
     */
    public function setExtractFormat(string $format): self
    {
        $this->setOption('extractformat', $format);

        return $this;
    }

    /**
     * Get the extractFormat parameter of the ExtractingRequestHandler.
     *
     * @return string|null
     */
    public function getExtractFormat(): ?string
    {
        return $this->getOption('extractformat');
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
    public function createDocument(array $fields = [], array $boosts = []): DocumentInterface
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
        parent::init();

        if (isset($this->options['fmap'])) {
            $this->setFieldMappings($this->options['fmap']);
        }
    }
}
