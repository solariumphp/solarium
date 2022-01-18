<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\AbstractBufferedUpdate\Event;

/**
 * Event definitions base class.
 *
 * @codeCoverageIgnore
 */
abstract class AbstractEvents
{
    /**
     * This event is called before a buffer flush.
     *
     * The event listener receives the buffer (array).
     *
     * @var string
     */
    public const PRE_FLUSH = AbstractPreFlush::class;

    /**
     * This event is called after a buffer flush.
     *
     * The event listener receives the Result.
     *
     * @var string
     */
    public const POST_FLUSH = AbstractPostFlush::class;

    /**
     * This event is called before a buffer commit.
     *
     * The event listener receives the buffer (array).
     *
     * @var string
     */
    public const PRE_COMMIT = AbstractPreCommit::class;

    /**
     * This event is called after a buffer commit.
     *
     * The event listener receives the Result.
     *
     * @var string
     */
    public const POST_COMMIT = AbstractPostCommit::class;

    /**
     * Not instantiable.
     */
    final private function __construct()
    {
    }
}
