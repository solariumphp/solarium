<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Query;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Collections result object.
 */
abstract class AbstractResult extends BaseResult
{
    /**
     * @var bool
     */
    protected $wasSuccessful = false;

    /**
     * @var string
     */
    protected $statusMessage = 'ERROR';

    /**
     * @return bool
     */
    public function getWasSuccessful(): bool
    {
        $this->parseResponse();

        return $this->wasSuccessful;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        $this->parseResponse();

        return $this->statusMessage;
    }
}
