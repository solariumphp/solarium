<?php

namespace Solarium\QueryType\Select;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Result\Result;

/**
 * Parse select response data.
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
        if (!in_array('Solarium\QueryType\Select\Result\DocumentInterface', $classes, true) &&
            !in_array('Solarium\QueryType\Update\Query\Document\DocumentInterface', $classes, true)
        ) {
            throw new RuntimeException('The result document class must implement a document interface');
        }

        $documents = [];
        if (isset($data['response']['docs'])) {
            foreach ($data['response']['docs'] as $doc) {
                $fields = (array) $doc;
                $documents[] = new $documentClass($fields);
            }
        }

        // component results
        $components = [];
        foreach ($query->getComponents() as $component) {
            $componentParser = $component->getResponseParser();
            if ($componentParser) {
                $components[$component->getType()] = $componentParser->parse($query, $component, $data);
            }
        }

        $numFound = null;

        if (isset($data['response']['numFound'])) {
            $numFound = $data['response']['numFound'];
        }

        $maxScore = null;

        if (isset($data['response']['maxScore'])) {
            $maxScore = $data['response']['maxScore'];
        }

        if (isset($data['nextCursorMark'])) {
            $nextCursorMark = $data['nextCursorMark'];
        } else {
            $nextCursorMark = null;
        }

        return $this->addHeaderInfo(
            $data,
            [
                'numfound' => $numFound,
                'maxscore' => $maxScore,
                'documents' => $documents,
                'components' => $components,
                'nextcursormark' => $nextCursorMark,
            ]
        );
    }
}
