<?php

namespace Solarium\QueryType\Server\Collections\ResponseParser;

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
     * @param AbstractResult $result
     *
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();

        $data = $this->addHeaderInfo($data, $data);
        return $data;
    }
}
