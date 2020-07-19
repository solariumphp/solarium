<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\ParallelExecution\Event;

/**
 * Event definitions.
 *
 * @codeCoverageIgnore
 */
class Events
{
    /**
     * This event is called just before parallel HTTP request execution, but after init work.
     * Intented for timing use only, there are no params.
     *
     * @var string
     */
    public const EXECUTE_START = ExecuteStart::class;

    /**
     * This event is called just after parallel HTTP request execution, before further result handling.
     * Intented for timing use only, there are no params.
     *
     * @var string
     */
    public const EXECUTE_END = ExecuteEnd::class;

    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }
}
