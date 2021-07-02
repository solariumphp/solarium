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
 * PreFlush event, see Events for details.
 */
class PreFlush extends Event
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
     * @var int|null
     */
    protected $commitWithin;

    /**
     * Event constructor.
     *
     * @param DocumentInterface[] $buffer
     * @param bool|null           $overwrite
     * @param int|null            $commitWithin
     */
    public function __construct(array $buffer, ?bool $overwrite, ?int $commitWithin)
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
     * @param int|null $commitWithin
     *
     * @return self Provides fluent interface
     */
    public function setCommitWithin(?int $commitWithin): self
    {
        $this->commitWithin = $commitWithin;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCommitWithin(): ?int
    {
        return $this->commitWithin;
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
}
