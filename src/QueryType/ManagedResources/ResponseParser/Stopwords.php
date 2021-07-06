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
 * Stopwords.
 */
class Stopwords extends ResponseParserAbstract implements ResponseParserInterface
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
        $wordSet = null;
        $data = [];
        $parsed = ['items' => []];
        $parsed = $this->parseStatus($parsed, $result);

        if ($parsed['wasSuccessful']) {
            $data = $result->getData();
            if (isset($data['wordSet'])) {
                $wordSet = $data['wordSet'];
            }
        }

        if (null !== $wordSet && !empty($wordSet)) {
            $parsed['items'] = $wordSet['managedList'];
            $parsed['initializedOn'] = $wordSet['initializedOn'];

            if (isset($wordSet['initArgs']['ignoreCase'])) {
                $parsed['ignoreCase'] = $wordSet['initArgs']['ignoreCase'];
            }

            if (isset($wordSet['updatedSinceInit'])) {
                $parsed['updatedSinceInit'] = $wordSet['updatedSinceInit'];
            }
        }

        $parsed = $this->addHeaderInfo($data, $parsed);

        return $parsed;
    }
}
