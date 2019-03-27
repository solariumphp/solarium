<?php

namespace Solarium\QueryType\Server\Api;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Server\CoreAdmin\Result\Result;

/**
 * Parse API response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
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
        $data = $this->addHeaderInfo($data, $data);
        return $data;
    }
}
