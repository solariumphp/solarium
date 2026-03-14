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
 * Wrapper class for a query to delete matching documents.
 */
class Query implements DeleteInterface
{
    /**
     * Query to delete matching documents.
     */
    protected string $query;

    /**
     * Constructor.
     *
     * @param string $query
     */
    public function __construct(string $query)
    {
        $this->query = $query;
    }

    /**
     * Get delete type.
     *
     * @return self::TYPE_QUERY
     */
    public function getType(): string
    {
        return DeleteInterface::TYPE_QUERY;
    }

    /**
     * Set query to delete matching documents.
     *
     * @param string $query
     *
     * @return self Provides fluent interface
     */
    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query to delete matching documents.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->query;
    }
}
