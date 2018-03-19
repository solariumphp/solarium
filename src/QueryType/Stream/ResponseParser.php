<?php

namespace Solarium\QueryType\Stream;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\StreamException;
use Solarium\QueryType\Select\Result\Result;

/**
 * Parse streaming expression response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();

        /*
         * @var Query
         */
        $query = $result->getQuery();

        // create document instances
        $documentClass = $query->getOption('documentclass');
        $classes = class_implements($documentClass);
        if (!in_array('Solarium\QueryType\Select\Result\DocumentInterface', $classes, true)) {
            throw new RuntimeException('The result document class must implement a document interface');
        }

        $documents = [];
        if (isset($data['result-set']['docs'])) {
            foreach ($data['result-set']['docs'] as $doc) {
                $fields = (array) $doc;
                if (isset($fields['EXCEPTION'])) {
                    // Use Solr's exception as message.
                    throw new StreamException($fields['EXCEPTION']);
                }
                if (isset($fields['EOF'])) {
                    // End of stream.
                    break;
                }
                $documents[] = new $documentClass($fields);
            }
            if (!isset($fields['EOF'])) {
                throw new StreamException('Streaming expression returned an incomplete result-set.');
            }
            $data['responseHeader']['QTime'] = $fields['RESPONSE_TIME'];
            $data['responseHeader']['status'] = 0;
        } else {
            throw new StreamException('Streaming expression did not return a result-set.');
        }

        return $this->addHeaderInfo(
            $data,
            [
                'numfound' => count($documents),
                'documents' => $documents,
            ]
        );
    }
}
