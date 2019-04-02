<?php

namespace Solarium\QueryType\Server\Collections\ResponseParser;

use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Server\Collections\ResponseParser;
use Solarium\QueryType\Server\Collections\Result\AbstractResult;

/**
 * Parse Collections API response data.
 */
class ClusterStatus extends ResponseParser
{
    /**
     * Parse response data.
     *
     * @param ResultInterface|AbstractResult $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();

        $data = $this->addHeaderInfo($data, $data);
        return $data;
    }
}
