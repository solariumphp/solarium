<?php

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\QueryType\Select\Result\DocumentInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * PreFlush event, see Events for details.
 */
class PreFlush extends Event
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
     * @var int
     */
    protected $commitWithin;

    /**
     * Event constructor.
     *
     * @param array $buffer
     * @param bool  $overwrite
     * @param int   $commitWithin
     */
    public function __construct($buffer, $overwrite, $commitWithin)
    {
        $this->buffer = $buffer;
        $this->overwrite = $overwrite;
        $this->commitWithin = $commitWithin;
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
     * @param int $commitWithin
     */
    public function setCommitWithin($commitWithin)
    {
        $this->commitWithin = $commitWithin;
    }

    /**
     * @return int
     */
    public function getCommitWithin()
    {
        return $this->commitWithin;
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
}
