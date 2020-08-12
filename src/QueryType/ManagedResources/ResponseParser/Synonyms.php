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
 * Synonyms.
 */
class Synonyms extends ResponseParserAbstract implements ResponseParserInterface
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
        $data = $result->getData();
        $synonymMappings = null;
        if (isset($data['synonymMappings'])) {
            $synonymMappings = $data['synonymMappings'];
        }

        $parsed = [];
        $items = [];

        if (null !== $synonymMappings && !empty($synonymMappings)) {
            foreach ($synonymMappings['managedMap'] as $term => $synonyms) {
                $items[] = new SynonymResult($term, $synonyms);
            }

            $parsed['items'] = $items;
            $parsed['initializedOn'] = $synonymMappings['initializedOn'];

            if (isset($synonymMappings['initArgs']['ignoreCase'])) {
                $parsed['ignoreCase'] = $synonymMappings['initArgs']['ignoreCase'];
            }

            if (isset($synonymMappings['initArgs']['format'])) {
                $parsed['format'] = $synonymMappings['initArgs']['format'];
            }

            if (isset($synonymMappings['updatedSinceInit'])) {
                $parsed['updatedSinceInit'] = $synonymMappings['updatedSinceInit'];
            }
        }

        $this->addHeaderInfo($data, $parsed);

        return $parsed;
    }
}
