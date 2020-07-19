<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedAdd\Event;

use Solarium\Core\Query\DocumentInterface;
use Symfony\Contracts\EventDispatcher\Event;

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
     * @var bool|null
     */
    protected $overwrite;

    /**
     * @var bool|null
     */
    protected $softCommit;

    /**
     * @var bool|null
     */
    protected $waitSearcher;

    /**
     * @var bool|null
     */
    protected $expungeDeletes;

    /**
     * Event constructor.
     *
     * @param DocumentInterface[] $buffer
     * @param bool|null           $overwrite
     * @param bool|null           $softCommit
     * @param bool|null           $waitSearcher
     * @param bool|null           $expungeDeletes
     */
    public function __construct(array $buffer, ?bool $overwrite, ?bool $softCommit, ?bool $waitSearcher, ?bool $expungeDeletes)
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
     * @param bool|null $expungeDeletes
     *
     * @return self Provides fluent interface
     */
    public function setExpungeDeletes(?bool $expungeDeletes): self
    {
        $this->expungeDeletes = $expungeDeletes;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getExpungeDeletes(): ?bool
    {
        return $this->expungeDeletes;
    }

    /**
     * Optionally override the value.
     *
     * @param bool|null $overwrite
     *
     * @return self Provides fluent interface
     */
    public function setOverwrite(?bool $overwrite): self
    {
        $this->overwrite = $overwrite;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getOverwrite(): ?bool
    {
        return $this->overwrite;
    }

    /**
     * Optionally override the value.
     *
     * @param bool|null $softCommit
     *
     * @return self Provides fluent interface
     */
    public function setSoftCommit(?bool $softCommit): self
    {
        $this->softCommit = $softCommit;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSoftCommit(): ?bool
    {
        return $this->softCommit;
    }

    /**
     * Optionally override the value.
     *
     * @param bool|null $waitSearcher
     *
     * @return self Provides fluent interface
     */
    public function setWaitSearcher(?bool $waitSearcher): self
    {
        $this->waitSearcher = $waitSearcher;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWaitSearcher(): ?bool
    {
        return $this->waitSearcher;
    }
}
