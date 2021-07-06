<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\ResponseParser;

use Solarium\Core\Query\Result\ResultInterface;

/**
 * Parse ManagedResources Remove Command response data.
 */
class Remove extends Command
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
        return $this->parseStatus([], $result);
    }
}
