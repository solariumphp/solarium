<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Collections\Result;

use Solarium\QueryType\Server\Query\AbstractResult;

/**
 * DeleteResult.
 */
class DeleteResult extends AbstractResult
{
    /**
     * Returns status of the request and the cores that were deleted.
     *
     * @return array status of the request and the cores that were deleted
     */
    public function getStatus(): array
    {
        $this->parseResponse();

        return $this->getData();
    }
}
