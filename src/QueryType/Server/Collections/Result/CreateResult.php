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
 * CreateResult.
 */
class CreateResult extends AbstractResult
{
    /**
     * Returns the status of the request and the new core names.
     *
     * {@internal Deprecated in Solarium 7. Shouldn't override generic method
     *            {@link \Solarium\Core\Query\Result\QueryType::getStatus()}
     *            that returns the status code from the response header.}
     *
     * @return array status of the request and the new core names
     *
     * @deprecated Will be removed in Solarium 8. Use {@link getCreateStatus()} instead.
     */
    public function getStatus(): array
    {
        return $this->getCreateStatus();
    }

    /**
     * Returns the status of the request and the new core names.
     *
     * @return array status of the request and the new core names
     */
    public function getCreateStatus(): array
    {
        $this->parseResponse();

        return $this->getData();
    }
}
