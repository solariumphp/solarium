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

        $data = parent::parse($result);

        // 'lucene' can contain the same key multiple times for multiValued fields.
        // A SimpleOrderedMap in Solr, it isn't represented as a flat array in JSON
        // and unlike a NamedList its output format can't be controlled with json.nl.
        // We can't rely on json_decode() if we don't want data loss for these fields.
        $data['doc']['lucene'] = JsonMachineItems::fromString(
            $result->getResponse()->getBody(),
            [
                // like json_decode('...', true), ExtJsonDecoder(true) returns arrays instead of objects
                'decoder' => new ExtJsonDecoder(true),
                'pointer' => '/doc/lucene',
            ]
        );

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

        $docInfo->setLucene($this->parseLucene($docData['lucene']));
        $docInfo->setSolr($this->parseSolr($docData['solr']));

        return $docInfo;
    }

    /**
     * @param JsonMachineItems $luceneData
     *
     * @return DocFieldInfo[]
     */
    protected function parseLucene(JsonMachineItems $luceneData): array
    {
        $lucene = [];

        foreach ($luceneData as $name => $details) {
            $fieldInfo = new DocFieldInfo($name);

            $fieldInfo->setType($details['type']);
            $fieldInfo->setSchema(new FlagList($details['schema'], $this->key));
            $fieldInfo->setFlags(new FlagList($details['flags'], $this->key));
            $fieldInfo->setValue($details['value']);
            $fieldInfo->setInternal($details['internal']);
            $fieldInfo->setBinary($details['binary'] ?? null);
            $fieldInfo->setDocFreq($details['docFreq'] ?? null);
            $fieldInfo->setTermVector($details['termVector'] ?? null);

            $lucene[] = $fieldInfo;
        }

        return $lucene;
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
