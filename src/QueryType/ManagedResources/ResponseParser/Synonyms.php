<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings;

class Synonyms extends ResponseParserAbstract implements ResponseParserInterface {

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

        if (isset($data['synonymMappings']) && !empty($data['synonymMappings'])) {
            $items = new SynonymMappings($data['synonymMappings']);
        }

        return $this->addHeaderInfo($data, ['items' => $items]);
    }
}