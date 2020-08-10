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
use Solarium\QueryType\ManagedResources\Result\Resources\Resource;

/**
 * Resources.
 */
class Resources extends ResponseParserAbstract implements ResponseParserInterface
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
