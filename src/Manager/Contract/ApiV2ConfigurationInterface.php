<?php

declare(strict_types=1);

namespace Solarium\Manager\Contract;

/**
 * Api V2 Configuration Interface.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
interface ApiV2ConfigurationInterface
{
    /**
     * Get a list of available commands for this api configuration.
     *
     * @return string[]
     */
    public function getCommands(): array;

    /**
     * Get a list of available sub-paths for this api configuration.
     *
     * @return string[]
     */
    public function getSubPaths(): array;

    /**
     * Get the handler's name for this api configuration.
     *
     * @return string
     */
    public function getHandler(): string;
}
