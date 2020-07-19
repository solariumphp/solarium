<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\Query\Action;

/**
 * AsyncActionInterface.
 */
interface AsyncActionInterface extends ActionInterface
{
    /**
     * Can be used to set a requestId to track this action with asynchronous processing.
     *
     * @param string $async
     *
     * @return self Provides fluent interface
     */
    public function setAsync(string $async): self;

    /**
     * Get the request-id for asynchronous processing.
     *
     * @return string
     */
    public function getAsync(): string;
}
