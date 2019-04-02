<?php

namespace Solarium\QueryType\Server\Collections;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Server\Collections\Result\AbstractResult;

/**
 * Parse Collections API response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param AbstractResult $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        $data = $this->parseStatus($data, $result);
        $data = $this->addHeaderInfo($data, $data);
        return $data;
    }

    /**
     * @param array          $data
     * @param AbstractResult $result
     *
     * @return array
     */
    protected function parseStatus(array $data, AbstractResult $result): array
    {
        $data['wasSuccessful'] = 200 === $result->getResponse()->getStatusCode();
        $data['statusMessage'] = $result->getResponse()->getStatusMessage();

        return $data;
    }
}
