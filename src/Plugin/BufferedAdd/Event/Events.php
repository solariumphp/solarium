<?php

declare(strict_types=1);

namespace Solarium\Plugin\BufferedAdd\Event;

/**
 * Event definitions.
 */
interface Events
{
    /**
     * This event is called before a buffer flush.
     *
     * The event listener receives the buffer (array) .
     *
     * @var string
     */
    public const PRE_FLUSH = PreFlush::class;

    /**
     * This event is called after a buffer flush.
     *
     * The event listener receives the Result
     *
     * @var string
     */
    public const POST_FLUSH = PostFlush::class;

    /**
     * This event is called before a buffer commit.
     *
     * The event listener receives the buffer (array) .
     *
     * @var string
     */
    public const PRE_COMMIT = PreCommit::class;

    /**
     * This event is called after a buffer commit.
     *
     * The event listener receives the Result
     *
     * @var string
     */
    public const POST_COMMIT = PostCommit::class;

    /**
     * This event is called when a new document is added.
     *
     * The event listener receives the Document
     *
     * @var string
     */
    public const ADD_DOCUMENT = AddDocument::class;
}
