<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Plugin\BufferedDelete\Delete;

use Solarium\Plugin\BufferedDelete\AbstractDelete;

/**
 * Wrapper class for the id of a document to delete.
 */
class Id extends AbstractDelete
{
    /**
     * Document id to delete.
     *
     * @var int|string
     */
    protected $id;

    /**
     * Constructor.
     *
     * @param int|string $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return AbstractDelete::TYPE_ID;
    }

    /**
     * Set document id to delete.
     *
     * @param int|string $id
     *
     * @return self Provides fluent interface
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get document id to delete.
     *
     * @return int|string
     */
    public function getId()
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
