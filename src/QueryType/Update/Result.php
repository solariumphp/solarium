<?php

namespace Solarium\QueryType\Update;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Update result.
 *
 * An update query only returns a query time and status. Both are accessible
 * using the methods provided by {@link Solarium\Result\Query}.
 *
 * For now this class only exists to distinguish the different result
 * types. It might get some extra behaviour in the future.
 */
class Result extends BaseResult
{
    /**
     * Status code returned by Solr.
     *
     * @var int
     */
    protected $status;

    /**
     * Solr index queryTime.
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @var int
     */
    protected $queryTime;

    /**
     * Get Solr status code.
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * @return int
     */
    public function getStatus(): int
    {
        $this->parseResponse();

        return $this->status;
    }

    /**
     * Get Solr query time.
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int
     */
    public function getQueryTime(): int
    {
        $this->parseResponse();

        return $this->queryTime;
    }
}
