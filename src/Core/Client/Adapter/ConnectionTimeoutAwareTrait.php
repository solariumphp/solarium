<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Client\Adapter;

/**
 * @internal
 */
trait ConnectionTimeoutAwareTrait
{
    /**
     * @var int
     */
    private $connectionTimeout = ConnectionTimeoutAwareInterface::DEFAULT_CONNECTION_TIMEOUT;

    /**
     * {@inheritdoc}
     */
    public function setConnectionTimeout(int $connectionTimeout): void
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionTimeout(): int
    {
        return $this->connectionTimeout;
    }
}
