<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\Adapter;

/**
 * Contract for Client Adapters that are aware of timeouts.
 */
interface TimeoutAwareInterface
{
    /**
     * Default timeout that should be respected by adapters implementing this interface.
     */
    public const DEFAULT_TIMEOUT = 5;

    /**
     * Fast timeout that should be used if the client should not wait for the result.
     *
     * @see \Solarium\Plugin\NoWaitForResponseRequest
     */
    public const FAST_TIMEOUT = 1;

    /**
     * @param int $timeoutInSeconds
     *
     * @return self Provides fluent interface
     */
    public function setTimeout(int $timeoutInSeconds): self;

    /**
     * @return int
     */
    public function getTimeout(): int;
}
