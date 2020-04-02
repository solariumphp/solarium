<?php

namespace Solarium\Core\Client\Adapter;

/**
 * @internal
 */
trait TimeoutAwareTrait
{
    /**
     * @var int|null
     */
    private $timeout;

    public function setTimeout(int $timeoutInSeconds): void
    {
        $this->timeout = $timeoutInSeconds;
    }

    public function getTimeout(): int
    {
        return $this->timeout ?? TimeoutAwareInterface::DEFAULT_TIMEOUT;
    }
}
