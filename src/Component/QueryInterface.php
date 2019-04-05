<?php

namespace Solarium\Component;

/**
 * Query Trait.
 */
interface QueryInterface
{
    /**
     * Set the query string.
     *
     * This overwrites the current value of a query or 'q' parameter.
     *
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery(string $query, array $bind = null): self;

    /**
     * Get query option.
     *
     * @return string|null
     */
    public function getQuery(): ?string;
}
