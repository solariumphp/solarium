<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;

/**
 * Parse ManagedResources Command response data.
 */
class Command extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param \Solarium\QueryType\ManagedResources\Result\Command $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        $parsed = $this->parseStatus([], $result);

        return $parsed;
    }
}
