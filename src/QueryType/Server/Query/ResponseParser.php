<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Query;

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

        return $data;
    }
}
