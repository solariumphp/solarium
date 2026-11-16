<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Shards;

use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\Index\Index;

/**
 * Retrieved shard details.
 */
class Shard
{
    protected string $address;

    protected Index $index;

    /**
     * @var array<string, FieldInfo>|null
     */
    protected ?array $fields = null;

    /**
     * Constructor.
     *
     * @param string $address
     */
    public function __construct(string $address)
    {
        $this->address = $address;
    }

    /**
     * Returns shard address.
     *
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * Returns index details.
     *
     * @return Index
     */
    public function getIndex(): Index
    {
        return $this->index;
    }

    /**
     * @param Index $index
     *
     * @return self Provides fluent interface
     */
    public function setIndex(Index $index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Returns per-field details.
     *
     * @return array<string, FieldInfo>|null
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    /**
     * @param array<string, FieldInfo>|null $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields(?array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function __toString(): string
    {
        return $this->address;
    }
}
