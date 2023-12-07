<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\ResponseParser;

use JsonMachine\Items as JsonMachineItems;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Luke\Result\Doc\DocFieldInfo;
use Solarium\QueryType\Luke\Result\Doc\DocInfo;
use Solarium\QueryType\Luke\Result\FlagList;
use Solarium\QueryType\Luke\Result\Result;

/**
 * Parse Luke doc response data.
 */
class Doc extends Index
{
    use InfoTrait;

    /**
     * @var ResultInterface
     */
    protected $result;

    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $this->result = $result;

        $query = $result->getQuery();

        if ($query::WT_PHPS === $query->getResponseWriter()) {
            // workaround for https://github.com/apache/solr/pull/2114
            $response = $result->getResponse();
            $response->setBody(str_replace('}s:4:"solr";i:0;a:', '}s:4:"solr";a:', $response->getBody()));
        }

        $data = parent::parse($result);

        // parse 'info' first so 'doc' can use it for flag lookups
        $data['infoResult'] = $this->parseInfo($data['info']);
        $data['docResult'] = $this->parseDoc($data['doc']);

        return $data;
    }

    /**
     * @param array $docData
     *
     * @return DocInfo
     */
    protected function parseDoc(array $docData): DocInfo
    {
        $docInfo = new DocInfo($docData['docId']);

        $query = $this->result->getQuery();

        // 'lucene' can contain the same key multiple times for multiValued fields.
        // How this is represented in the response body depends on Solr's ResponseWriter.
        if ($query::WT_JSON === $query->getResponseWriter()) {
            // A SimpleOrderedMap in Solr isn't represented as a flat array in JSON
            // and unlike a NamedList its output format can't be controlled with json.nl.
            // We can't rely on json_decode() if we don't want data loss for these fields.
            $docData['lucene'] = JsonMachineItems::fromString(
                $this->result->getResponse()->getBody(),
                [
                    // like json_decode('...', true), ExtJsonDecoder(true) returns arrays instead of objects
                    'decoder' => new ExtJsonDecoder(true),
                    'pointer' => '/doc/lucene',
                ]
            );

            $docInfo->setLucene($this->parseLuceneJson($docData['lucene']));
        } else {
            $docInfo->setLucene($this->parseLucenePhps($docData['lucene']));
        }

        $docInfo->setSolr($this->parseSolr($docData['solr']));

        return $docInfo;
    }

    /**
     * @param JsonMachineItems $luceneData
     *
     * @return DocFieldInfo[]
     */
    protected function parseLuceneJson(JsonMachineItems $luceneData): array
    {
        $lucene = [];

        foreach ($luceneData as $name => $details) {
            $lucene[] = $this->parseLuceneField($name, $details);
        }

        return $lucene;
    }

    /**
     * @param array $luceneData
     *
     * @return DocFieldInfo[]
     */
    protected function parseLucenePhps(array $luceneData): array
    {
        $lucene = [];
        $prevName = '';

        foreach ($luceneData as $name => $details) {
            if (str_starts_with($name, $prevName.' ')) {
                // field name was mangled by Solr's ResponseWriter to avoid repeats in the output
                $name = $prevName;
            }

            $lucene[] = $this->parseLuceneField($name, $details);
            $prevName = $name;
        }

        return $lucene;
    }

    /**
     * @param string $name
     * @param array  $details
     *
     * @return DocFieldInfo
     */
    protected function parseLuceneField(string $name, array $details): DocFieldInfo
    {
        $fieldInfo = new DocFieldInfo($name);

        $fieldInfo->setType($details['type']);
        $fieldInfo->setSchema(new FlagList($details['schema'], $this->key));
        $fieldInfo->setFlags(new FlagList($details['flags'], $this->key));
        $fieldInfo->setValue($details['value']);
        $fieldInfo->setInternal($details['internal']);
        $fieldInfo->setBinary($details['binary'] ?? null);
        $fieldInfo->setDocFreq($details['docFreq'] ?? null);
        $fieldInfo->setTermVector($details['termVector'] ?? null);

        return $fieldInfo;
    }

    /**
     * @param array $solrData
     *
     * @throws RuntimeException
     *
     * @return DocumentInterface
     */
    protected function parseSolr(array $solrData): DocumentInterface
    {
        $documentClass = $this->result->getQuery()->getDocumentClass();
        $classes = class_implements($documentClass);
        if (!\in_array(DocumentInterface::class, $classes, true)) {
            throw new RuntimeException('The result document class must implement a document interface');
        }

        return new $documentClass($solrData);
    }
}
