<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;

class Stopwords extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param ResultInterface $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        $wordSet = null;
        if (isset($data['wordSet'])) {
            $wordSet = $data['wordSet'];
        }

        $parsed = [];

        if (null !== $wordSet && !empty($wordSet)) {
            $parsed['items'] = $wordSet['managedList'];
            $parsed['ignoreCase'] = $wordSet['initArgs']['ignoreCase'];
            $parsed['initializedOn'] = $wordSet['initializedOn'];
        }

        $this->addHeaderInfo($data, $parsed);

        return $parsed;
    }
}
