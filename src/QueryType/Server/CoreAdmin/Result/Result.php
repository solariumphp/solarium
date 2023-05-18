<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * CoreAdmin result object.
 */
class Result extends BaseResult
{
    /**
     * @var array
     */
    protected $status;

    /**
     * @var array
     */
    protected $initFailures;

    /**
     * InitFailureResult collection.
     *
     * @var InitFailureResult[]
     */
    protected $initFailureResults;

    /**
     * StatusResult collection when multiple statuses have been requested.
     *
     * @var StatusResult[]|null
     */
    protected $statusResults;

    /**
     * Status result when the status only for one core as requested.
     *
     * @var StatusResult
     */
    protected $statusResult;

    /**
     * @var bool
     */
    protected $wasSuccessful = false;

    /**
     * @var string
     */
    protected $statusMessage = 'ERROR';

    /**
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return bool
     */
    public function getWasSuccessful(): bool
    {
        $this->parseResponse();

        return $this->wasSuccessful;
    }

    /**
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return string
     */
    public function getStatusMessage(): string
    {
        $this->parseResponse();

        return $this->statusMessage;
    }

    /**
     * Returns the init failure result objects.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return InitFailureResult[]|null
     */
    public function getInitFailureResults(): ?array
    {
        $this->parseResponse();

        return $this->initFailureResults;
    }

    /**
     * Returns the status result objects for all requested core statuses.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return StatusResult[]|null
     */
    public function getStatusResults(): ?array
    {
        $this->parseResponse();

        return $this->statusResults;
    }

    /**
     * Retrieves the status of the core, only available when the query was filtered to a core in the status action.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return StatusResult|null
     */
    public function getStatusResult(): ?StatusResult
    {
        $this->parseResponse();

        return $this->statusResult;
    }

    /**
     * @param string $coreName
     *
     * @return StatusResult|null
     */
    public function getStatusResultByCoreName(string $coreName): ?StatusResult
    {
        return $this->statusResults[$coreName] ?? null;
    }
}
