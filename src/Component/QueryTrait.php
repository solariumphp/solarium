<?php

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
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery($query, $bind = null)
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
    public function getQuery()
    {
        return $this->getOption('query');
    }
}
