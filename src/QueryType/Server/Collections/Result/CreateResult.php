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
     * @return array status of the request and the new core names
     */
    public function getStatus(): array
    {
        $this->parseResponse();

        return $this->getData();
    }
}
