<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\Adapter;

/**
 * Contract for Http Adapters that are aware of timeouts.
 */
interface TimeoutAwareInterface
{
    /**
     * Default timeout that should be respected by adapters implementing this interface.
     */
    public const DEFAULT_TIMEOUT = 5;

    /**
     * @param int $timeoutInSeconds
     */
    public function setTimeout(int $timeoutInSeconds): void;

    /**
     * @return int
     */
    public function getTimeout(): int;
}
