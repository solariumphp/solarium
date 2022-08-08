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
 * @internal
 */
trait TimeoutAwareTrait
{
    /**
     * @var int
     */
    private $timeout = TimeoutAwareInterface::DEFAULT_TIMEOUT;

    /**
     * {@inheritdoc}
     */
    public function setTimeout(int $timeoutInSeconds)
    {
        $this->timeout = $timeoutInSeconds;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
