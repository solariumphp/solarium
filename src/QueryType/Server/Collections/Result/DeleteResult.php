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
#[\AllowDynamicProperties]
class DeleteResult extends AbstractResult
{
    /**
     * Returns status of the request and the cores that were deleted.
     *
     * {@internal Deprecated in Solarium 7. Shouldn't override generic method
     *            {@link \Solarium\Core\Query\Result\QueryType::getStatus()}
     *            that returns the status code from the response header.}
     *
     * @return array status of the request and the cores that were deleted
     *
     * @deprecated Will be removed in Solarium 8. Use {@link getDeleteStatus()} instead.
     */
    public function getStatus(): array
    {
        return $this->getDeleteStatus();
    }

    /**
     * Returns status of the request and the cores that were deleted.
     *
     * @return array status of the request and the cores that were deleted
     */
    public function getDeleteStatus(): array
    {
        $this->parseResponse();

        return $this->getData();
    }
}
