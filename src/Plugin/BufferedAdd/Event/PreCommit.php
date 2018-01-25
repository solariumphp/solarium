<?php

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\QueryType\Select\Result\DocumentInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PreCommit event, see Events for details.
 */
class PreCommit extends Event
{
    /**
     * @var DocumentInterface[]
     */
    protected $buffer;

    /**
     * @var bool
     */
    protected $overwrite;

    /**
     * @var bool
     */
    protected $softCommit;

    /**
     * @var bool
     */
    protected $waitSearcher;

    /**
     * @var bool
     */
    protected $expungeDeletes;

    /**
     * Event constructor.
     *
     * @param array $buffer
     * @param bool  $overwrite
     * @param bool  $softCommit
     * @param bool  $waitSearcher
     * @param bool  $expungeDeletes
     */
    public function __construct($buffer, $overwrite, $softCommit, $waitSearcher, $expungeDeletes)
    {
        $this->buffer = $buffer;
        $this->overwrite = $overwrite;
        $this->softCommit = $softCommit;
        $this->waitSearcher = $waitSearcher;
        $this->expungeDeletes = $expungeDeletes;
    }

    /**
     * Get the buffer for this event.
     *
     * @return DocumentInterface[]
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Set the buffer for this event, this way you can alter the buffer before it is committed to Solr.
     *
     * @param array $buffer
     */
    public function setBuffer($buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $expungeDeletes
     */
    public function setExpungeDeletes($expungeDeletes)
    {
        $this->expungeDeletes = $expungeDeletes;
    }

    /**
     * @return bool
     */
    public function getExpungeDeletes()
    {
        return $this->expungeDeletes;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $overwrite
     */
    public function setOverwrite($overwrite)
    {
        $this->overwrite = $overwrite;
    }

    /**
     * @return bool
     */
    public function getOverwrite()
    {
        return $this->overwrite;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $softCommit
     */
    public function setSoftCommit($softCommit)
    {
        $this->softCommit = $softCommit;
    }

    /**
     * @return bool
     */
    public function getSoftCommit()
    {
        return $this->softCommit;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $waitSearcher
     */
    public function setWaitSearcher($waitSearcher)
    {
        $this->waitSearcher = $waitSearcher;
    }

    /**
     * @return bool
     */
    public function getWaitSearcher()
    {
        return $this->waitSearcher;
    }
}
