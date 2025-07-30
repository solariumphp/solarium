<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Ping;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Ping query result.
 */
class Result extends BaseResult
{
    /**
     * @var string
     */
    protected $status;

    /**
     * Return the ping status.
     *
     * This is different from the Solr status code you get with {@link getStatus()}.
     *
     * @return string
     */
    public function getPingStatus(): string
    {
        $this->parseResponse();

        return $this->status;
    }

    /**
     * Return whether the node was connected with ZooKeeper for a distributed request.
     *
     * @return bool|null
     */
    public function getZkConnected(): ?bool
    {
        $this->parseResponse();

        return $this->responseHeader['zkConnected'] ?? null;
    }
}
