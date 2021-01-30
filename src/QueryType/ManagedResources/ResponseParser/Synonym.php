<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms as SynonymResult;

/**
 * Synonym.
 */
class Synonym extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param \Solarium\Core\Query\Result\ResultInterface $result
     *
     * @throws \Solarium\Exception\RuntimeException
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = [];
        $parsed = ['items' => []];
        $parsed = $this->parseStatus($parsed, $result);

        if ($parsed['wasSuccessful']) {
            $data = $result->getData();
            $items = [];

            foreach ($data as $term => $synonyms) {
                if ('responseHeader' !== $term) {
                    $items[] = new SynonymResult($term, $synonyms);
                }
            }

            $parsed['items'] = $items;
        }

        $parsed = $this->addHeaderInfo($data, $parsed);

        return $parsed;
    }
}
