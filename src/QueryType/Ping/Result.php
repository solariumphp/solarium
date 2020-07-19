<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Ping;

use Solarium\Core\Query\Result\Result as BaseResult;

/**
 * Ping query result.
 *
 * A ping query has no useful result so only a dummy status var is available.
 * If you don't get an exception for a ping query it was successful.
 */
class Result extends BaseResult
{
    /**
     * Get Solr status code.
     *
     * Since no status is returned for a ping, a default of 0 is used.
     * If you don't get an exception for a ping query it was successful.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return 0;
    }
}
