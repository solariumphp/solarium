<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\TermVector as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\TermVector as ResponseParser;

/**
 * Term Vector component.
 *
 * @see https://solr.apache.org/guide/the-term-vector-component.html
 */
class TermVector extends AbstractComponent
{
    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_TERMVECTOR;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Set the Lucene document ID(s) to return term vectors for.
     *
     * For multiple IDs use a comma-separated string or array.
     *
     * @param string|array $docIds
     *
     * @return self Provides fluent interface
     */
    public function setDocIds($docIds): self
    {
        if (\is_string($docIds)) {
            $docIds = explode(',', $docIds);
            $docIds = array_map('trim', $docIds);
        }

        return $this->setOption('docids', $docIds);
    }

    /**
     * Get the Lucene document ID(s) to return term vectors for.
     *
     * @return array
     */
    public function getDocIds(): array
    {
        return $this->getOption('docids') ?? [];
    }

    /**
     * Set the field name(s) to return term vectors for.
     *
     * For multiple fields use a comma-separated string or array.
     *
     * @param string|array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        return $this->setOption('fields', $fields);
    }

    /**
     * Get the field name(s) to return term vectors for.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->getOption('fields') ?? [];
    }

    /**
     * Set enable all boolean parameters.
     *
     * @param bool $all
     *
     * @return self Provides fluent interface
     */
    public function setAll(bool $all): self
    {
        $this->setOption('all', $all);

        return $this;
    }

    /**
     * Get enable all boolean parameters.
     *
     * @return bool|null
     */
    public function getAll(): ?bool
    {
        return $this->getOption('all');
    }

    /**
     * Set return document frequency (DF) of the term in the collection.
     *
     * This can be computationally expensive.
     *
     * @param bool $df
     *
     * @return self Provides fluent interface
     */
    public function setDocumentFrequency(bool $df): self
    {
        $this->setOption('documentfrequency', $df);

        return $this;
    }

    /**
     * Get return document frequency (DF) of the term in the collection.
     *
     * @return bool|null
     */
    public function getDocumentFrequency(): ?bool
    {
        return $this->getOption('documentfrequency');
    }

    /**
     * Set return offset information for each term in the document.
     *
     * @param bool $offsets
     *
     * @return self Provides fluent interface
     */
    public function setOffsets(bool $offsets): self
    {
        $this->setOption('offsets', $offsets);

        return $this;
    }

    /**
     * Get return offset information for each term in the document.
     *
     * @return bool|null
     */
    public function getOffsets(): ?bool
    {
        return $this->getOption('offsets');
    }

    /**
     * Set return position information.
     *
     * @param bool $postitions
     *
     * @return self Provides fluent interface
     */
    public function setPositions(bool $postitions): self
    {
        $this->setOption('positions', $postitions);

        return $this;
    }

    /**
     * Get return position information.
     *
     * @return bool|null
     */
    public function getPositions(): ?bool
    {
        return $this->getOption('positions');
    }

    /**
     * Set return payload information.
     *
     * @param bool $payloads
     *
     * @return self Provides fluent interface
     */
    public function setPayloads(bool $payloads): self
    {
        $this->setOption('payloads', $payloads);

        return $this;
    }

    /**
     * Get return payload information.
     *
     * @return bool|null
     */
    public function getPayloads(): ?bool
    {
        return $this->getOption('payloads');
    }

    /**
     * Set return document term frequency (TF) for each term in the document.
     *
     * @param bool $tf
     *
     * @return self Provides fluent interface
     */
    public function setTermFrequency(bool $tf): self
    {
        $this->setOption('termfrequency', $tf);

        return $this;
    }

    /**
     * Get return document term frequency (TF) for each term in the document.
     *
     * @return bool|null
     */
    public function getTermFrequency(): ?bool
    {
        return $this->getOption('termfrequency');
    }

    /**
     * Set calculate TF / DF (i.e., TF * IDF) for each term.
     *
     * Requires both setTermFrequency(true) and setDocumentFrequency(true).
     *
     * This can be computationally expensive.
     *
     * @param bool $tfIdf
     *
     * @return self Provides fluent interface
     */
    public function setTermFreqInverseDocFreq(bool $tfIdf): self
    {
        $this->setOption('termfreqinversedocfreq', $tfIdf);

        return $this;
    }

    /**
     * Get calculate TF / DF (i.e., TF * IDF) for each term.
     *
     * @return bool|null
     */
    public function getTermFreqInverseDocFreq(): ?bool
    {
        return $this->getOption('termfreqinversedocfreq');
    }

    /**
     * Initialize options.
     *
     * {@internal Options that set a list of ids or fields need additional setup work
     *            because they can be an array or a comma separated string.}
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'docids':
                    $this->setDocIds($value);
                    break;
                case 'fields':
                    $this->setFields($value);
                    break;
            }
        }
    }
}
