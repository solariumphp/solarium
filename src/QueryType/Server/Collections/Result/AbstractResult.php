<?php

namespace Solarium\QueryType\Server\Collections\Result;

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
