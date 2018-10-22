<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\Result;

class Synonyms extends ResponseParserAbstract implements ResponseParserInterface
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
        $synonymMappings = null;
        if(isset($data['synonymMappings'])) {
            $synonymMappings = $data['synonymMappings'];
        }

        $parsed = [];
        $items = [];

        if ($synonymMappings !== null && !empty($synonymMappings)) {
            foreach ($synonymMappings['managedMap'] as $term => $synonyms) {
                $items[] = new \Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms($term, $synonyms);
            }

            $parsed['items'] = $items;
            $parsed['ignoreCase'] = $synonymMappings['initArgs']['ignoreCase'];
            $parsed['initializedOn'] = $synonymMappings['initializedOn'];
            $parsed['updatedSinceInit'] = $synonymMappings['updatedSinceInit'];
        }

        $this->addHeaderInfo($data, $parsed);

        return $parsed;
    }
}