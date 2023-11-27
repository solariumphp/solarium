<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Api;

use Solarium\QueryType\Server\Query\AbstractResult;

/**
 * API result.
 */
class Result extends AbstractResult
{
    /**
     * @var string|null
     */
    protected $WARNING;

    /**
     * Returns a warning for the result or null if Solr didn't set a warning.
     *
     * @return string|null
     */
    public function getWarning(): ?string
    {
        $this->parseResponse();

        return $this->WARNING;
    }
}
