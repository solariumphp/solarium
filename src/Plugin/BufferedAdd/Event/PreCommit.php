<?php

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\Core\Query\DocumentInterface;
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
    public function __construct(array $buffer, bool $overwrite, bool $softCommit, bool $waitSearcher, bool $expungeDeletes)
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
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * Set the buffer for this event, this way you can alter the buffer before it is committed to Solr.
     *
     * @param DocumentInterface[] $buffer
     *
     * @return self Provides fluent interface
     */
    public function setBuffer(array $buffer): self
    {
        $this->buffer = $buffer;
        return $this;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $expungeDeletes
     *
     * @return self Provides fluent interface
     */
    public function setExpungeDeletes(bool $expungeDeletes): self
    {
        $this->expungeDeletes = $expungeDeletes;
        return $this;
    }

    /**
     * @return bool
     */
    public function getExpungeDeletes(): bool
    {
        return $this->expungeDeletes;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite(bool $overwrite): self
    {
        $this->overwrite = $overwrite;
        return $this;
    }

    /**
     * @return bool
     */
    public function getOverwrite(): bool
    {
        return $this->overwrite;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $softCommit
     *
     * @return self Provides fluent interface
     */
    public function setSoftCommit(bool $softCommit): self
    {
        $this->softCommit = $softCommit;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSoftCommit(): bool
    {
        return $this->softCommit;
    }

    /**
     * Optionally override the value.
     *
     * @param bool $waitSearcher
     *
     * @return self Provides fluent interface
     */
    public function setWaitSearcher(bool $waitSearcher): self
    {
        $this->waitSearcher = $waitSearcher;
        return $this;
    }

    /**
     * @return bool
     */
    public function getWaitSearcher(): bool
    {
        return $this->waitSearcher;
    }
}
