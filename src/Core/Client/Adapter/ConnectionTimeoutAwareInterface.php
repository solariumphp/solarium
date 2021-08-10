<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\Adapter;

/**
 * Contract for Http Adapters that are aware of connection timeouts.
 */
interface ConnectionTimeoutAwareInterface
{
    /**
     * @param int|null $timeoutInSeconds
     */
    public function setConnectionTimeout(?int $timeoutInSeconds): void;

    /**
     * @return int|null
     */
    public function getConnectionTimeout(): ?int;
}
