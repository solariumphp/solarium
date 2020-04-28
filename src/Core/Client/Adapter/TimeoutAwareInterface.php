<?php

namespace Solarium\Core\Client\Adapter;

/**
 * Contract for Http Adapters that are aware of timeouts.
 */
interface TimeoutAwareInterface
{
    /**
     * default timeout that should be respected by adapters implementing this interface.
     */
    public const DEFAULT_TIMEOUT = 5;

    public function setTimeout(int $timeoutInSeconds): void;

    public function getTimeout(): int;
}
