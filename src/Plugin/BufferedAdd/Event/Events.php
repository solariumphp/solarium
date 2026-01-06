<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\Plugin\AbstractBufferedUpdate\Event\AbstractEvents;

/**
 * Event definitions.
 *
 * @codeCoverageIgnore
 */
class Events extends AbstractEvents
{
    /**
     * This event is called when a new document is added to the buffer.
     *
     * The event listener receives the Document.
     */
    public const ADD_DOCUMENT = AddDocument::class;

    /**
     * This event is called before a buffer flush.
     *
     * The event listener receives the buffer (array).
     */
    public const PRE_FLUSH = PreFlush::class;

    /**
     * This event is called after a buffer flush.
     *
     * The event listener receives the Result.
     */
    public const POST_FLUSH = PostFlush::class;

    /**
     * This event is called before a buffer commit.
     *
     * The event listener receives the buffer (array).
     */
    public const PRE_COMMIT = PreCommit::class;

    /**
     * This event is called after a buffer commit.
     *
     * The event listener receives the Result.
     */
    public const POST_COMMIT = PostCommit::class;
}
