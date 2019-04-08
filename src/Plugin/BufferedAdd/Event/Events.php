<?php

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
    const PRE_FLUSH = 'solarium.bufferedAdd.preFlush';

    /**
     * This event is called after a buffer flush.
     *
     * The event listener receives the Result
     *
     * @var string
     */
    const POST_FLUSH = 'solarium.bufferedAdd.postFlush';

    /**
     * This event is called before a buffer commit.
     *
     * The event listener receives the buffer (array) .
     *
     * @var string
     */
    const PRE_COMMIT = 'solarium.bufferedAdd.preCommit';

    /**
     * This event is called after a buffer commit.
     *
     * The event listener receives the Result
     *
     * @var string
     */
    const POST_COMMIT = 'solarium.bufferedAdd.postCommit';

    /**
     * This event is called when a new document is added.
     *
     * The event listener receives the Document
     *
     * @var string
     */
    const ADD_DOCUMENT = 'solarium.bufferedAdd.addDocument';
}
