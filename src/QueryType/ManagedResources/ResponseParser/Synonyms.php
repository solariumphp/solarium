<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Exception\RuntimeException;

class Synonyms extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param ResultInterface $result
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        $synonymMappings = null;
        if (isset($data['synonymMappings'])) {
            $synonymMappings = $data['synonymMappings'];
        }

        $parsed = [];
        $items = [];

        if (null !== $synonymMappings && !empty($synonymMappings)) {
            foreach ($synonymMappings['managedMap'] as $term => $synonyms) {
                $items[] = new \Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms($term, $synonyms);
            }

            $parsed['items'] = $items;
            $parsed['ignoreCase'] = $synonymMappings['initArgs']['ignoreCase'];
            $parsed['initializedOn'] = $synonymMappings['initializedOn'];
        }

        $this->addHeaderInfo($data, $parsed);

        return $parsed;
    }
}
