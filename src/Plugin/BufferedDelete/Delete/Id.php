<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete\Delete;

use Solarium\Plugin\BufferedDelete\DeleteInterface;

/**
 * Wrapper class for the id of a document to delete.
 */
class Id implements DeleteInterface
{
    /**
     * Document id to delete.
     */
    protected int|string $id;

    /**
     * Constructor.
     *
     * @param int|string $id
     */
    public function __construct(int|string $id)
    {
        $this->id = $id;
    }

    /**
     * Get delete type.
     *
     * @return self::TYPE_ID
     */
    public function getType(): string
    {
        return DeleteInterface::TYPE_ID;
    }

    /**
     * Set document id to delete.
     *
     * @param int|string $id
     *
     * @return self Provides fluent interface
     */
    public function setId(int|string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get document id to delete.
     *
     * @return int|string
     */
    public function getId(): int|string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->id;
    }
}
