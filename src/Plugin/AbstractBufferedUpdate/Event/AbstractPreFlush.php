<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\AbstractBufferedUpdate\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * PreFlush event base class, see {@see AbstractEvents} for details.
 */
abstract class AbstractPreFlush extends Event
{
    /**
     * @var array
     */
    protected $buffer;

    /**
     * Event constructor.
     *
     * @param array $buffer
     */
    public function __construct(array $buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * Get the buffer for this event.
     *
     * @return array
     */
    public function getBuffer(): array
    {
        return $this->buffer;
    }

    /**
     * Set the buffer for this event, this way you can alter the buffer before it is flushed to Solr.
     *
     * @param array $buffer
     *
     * @return self Provides fluent interface
     */
    public function setBuffer(array $buffer): self
    {
        $this->buffer = $buffer;

        return $this;
    }
}
