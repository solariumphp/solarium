<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\ResponseParser;

use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Server\Collections\Result\AbstractResult;
use Solarium\QueryType\Server\Query\ResponseParser;

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

        return $data;
    }
}
