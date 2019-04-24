<?php

namespace Solarium\QueryType\Graph;

use Solarium\Core\Query\Result\Result as BaseResult;

/**
 * Graph query result.
 */
class Result extends BaseResult
{
    /**
     * Get Solr status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get Solr response body.
     *
     * @return array The response body
     */
    public function getData(): array
    {
        return ['body' => $this->response->getBody()];
    }

    /**
     * Get Solr response data in GraphML format.
     *
     * More expressive convenience method that just call getData().
     *
     * @return string GraphML XML document
     */
    public function getGraphML(): string
    {
        return $this->response->getBody();
    }
}
