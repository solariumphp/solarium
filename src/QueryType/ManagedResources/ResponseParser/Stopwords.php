<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\Result;

class Stopwords extends ResponseParserAbstract implements ResponseParserInterface
{

    /**
     * Parse response data.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();
        $wordSet = null;
        if(isset($data['wordSet'])) {
            $wordSet = $data['wordSet'];
        }

        $parsed = [];

        if ($wordSet !== null && !empty($wordSet)) {
            $items = $wordSet['managedList'];

            $parsed['items'] = $items;
            $parsed['ignoreCase'] = $wordSet['initArgs']['ignoreCase'];
            $parsed['initializedOn'] = $wordSet['initializedOn'];
            $parsed['updatedSinceInit'] = $wordSet['updatedSinceInit'];
        }

        $this->addHeaderInfo($data, $parsed);

        return $parsed;
    }
}