<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Stream;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
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
     * @param Result|ResultInterface $result
     *
     * @throws \Solarium\Exception\RuntimeException
     * @throws \Solarium\Exception\StreamException
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();

        /** @var Query $query */
        $query = $result->getQuery();

        // create document instances
        $documentClass = $query->getOption('documentclass');
        $classes = class_implements($documentClass);
        if (!\in_array(DocumentInterface::class, $classes, true)) {
            throw new RuntimeException('The result document class must implement DocumentInterface');
        }

        $documents = [];
        if (isset($data['result-set']['docs'])) {
            foreach ($data['result-set']['docs'] as $doc) {
                $fields = (array) $doc;
                if (isset($fields['EXCEPTION'])) {
                    // Use Solr's exception as message.
                    $e = new StreamException($fields['EXCEPTION']);
                    $e->setExpression($query->getExpression());
                    throw $e;
                }
                if (isset($fields['EOF'])) {
                    // End of stream.
                    break;
                }
                $documents[] = new $documentClass($fields);
            }
            if (!isset($fields['EOF'])) {
                $e = new StreamException('Streaming expression returned an incomplete result-set.');
                $e->setExpression($query->getExpression());
                throw $e;
            }
            $data['responseHeader']['QTime'] = $fields['RESPONSE_TIME'];
            $data['responseHeader']['status'] = 0;
        } else {
            $e = new StreamException('Streaming expression did not return a result-set.');
            $e->setExpression($query->getExpression());
            throw $e;
        }

        return $this->addHeaderInfo(
            $data,
            [
                'numfound' => \count($documents),
                'documents' => $documents,
            ]
        );
    }
}
