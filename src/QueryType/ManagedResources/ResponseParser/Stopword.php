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

/**
 * Stopword.
 */
class Stopword extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param \Solarium\Core\Query\Result\ResultInterface $result
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

            foreach ($data as $term => $stopword) {
                if ('responseHeader' !== $term) {
                    $items[] = $stopword;
                }
            }

            $parsed['items'] = $items;
        }

        return $parsed;
    }
}
