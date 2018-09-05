<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;

class Stopwords extends ResponseParserAbstract implements ResponseParserInterface {

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

        $items = [];

        if (isset($data['wordSet']) && !empty($data['wordSet'])) {
            $items = new WordSet($data['wordSet']);
        }

        return $this->addHeaderInfo($data, ['items' => $items]);
    }
}