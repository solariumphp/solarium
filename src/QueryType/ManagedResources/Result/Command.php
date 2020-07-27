<?php

namespace Solarium\QueryType\ManagedResources\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * ManagedResources Command result object.
 */
class Command extends BaseResult
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
