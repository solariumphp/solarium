<?php

namespace Solarium\QueryType\Stream;

use Solarium\Core\Query\Result\Result as BaseResult;

/**
 * Stream query result.
 */
class Result extends BaseResult
{
    /**
     * Get Solr status code.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get Solr response body.
     *
     * @return string A JSON encoded string
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * Get Solr response data.
     *
     * Includes a lazy loading mechanism: JSON body data is decoded on first use and then saved for reuse.
     *
     * @return array
     */
    public function getData()
    {
        if (null === $this->data) {
            $this->data = json_decode($this->response->getBody(), true);
        }

        return $this->data;
    }
}
