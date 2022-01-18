<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete\Event;

use Solarium\Plugin\BufferedDelete\Delete\Id;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * AddDeleteById event, see {@see Events} for details.
 */
class AddDeleteById extends Event
{
    /**
     * @var Id
     */
    protected $id;

    /**
     * Event constructor.
     *
     * @param Id $id
     */
    public function __construct(Id $id)
    {
        $this->id = $id;
    }

    /**
     * Get the id for this event.
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id->getId();
    }

    /**
     * Set the id for this event, this way you can alter the id before it is sent to Solr.
     *
     * @param int|string $id
     *
     * @return self Provides fluent interface
     */
    public function setId($id): self
    {
        $this->id->setId($id);

        return $this;
    }
}
