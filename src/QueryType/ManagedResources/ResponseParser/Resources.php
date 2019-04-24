<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\ManagedResources\Result\Resources\Resource;

class Resources extends ResponseParserAbstract implements ResponseParserInterface
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

        $items = [];

        if (isset($data['managedResources']) && !empty($data['managedResources'])) {
            foreach ($data['managedResources'] as $resource) {
                $items[] = new Resource($resource);
            }
        }

        return $this->addHeaderInfo($data, ['items' => $items]);
    }
}
