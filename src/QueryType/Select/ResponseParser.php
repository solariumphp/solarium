<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Select;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Query\Query;
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
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();

        /** @var Query $query */
        $query = $result->getQuery();

        // create document instances
        $documentClass = $query->getOption('documentclass');
        $classes = class_implements($documentClass);
        if (!\in_array(DocumentInterface::class, $classes, true)) {
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

        return [
            'numfound' => $data['response']['numFound'] ?? null,
            'maxscore' => $data['response']['maxScore'] ?? null,
            'documents' => $documents,
            'components' => $components,
            'nextcursormark' => $data['nextCursorMark'] ?? null,
        ];
    }
}
