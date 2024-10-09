<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

/**
 * Query Trait.
 */
trait QueryTrait
{
    /**
     * Set the query string.
     *
     * This overwrites the current value of a query or 'q' parameter.
     *
     * @param string     $query
     * @param array|null $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery(string $query, ?array $bind = null): QueryInterface
    {
        if (null !== $bind) {
            $helper = $this->getHelper();
            $query = $helper->assemble($query, $bind);
        }

        return $this->setOption('query', trim($query));
    }

    /**
     * Get query option.
     *
     * @return string|null
     */
    public function getQuery(): ?string
    {
        return $this->getOption('query');
    }
}
