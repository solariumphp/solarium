<?php

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\ManagedResources\Result\Resources\ResourcesList;

class Resources extends ResponseParserAbstract implements ResponseParserInterface {

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

        if (isset($data['managedResources']) && !empty($data['managedResources'])) {
            $items = new ResourcesList($data['managedResources']);
        }

        return $this->addHeaderInfo($data, ['items' => $items]);
    }
}