<?php

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

    public function setTimeout(int $timeoutInSeconds): void
    {
        $this->timeout = $timeoutInSeconds;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
