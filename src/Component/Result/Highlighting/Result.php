<?php

namespace Solarium\Component\Result\Highlighting;

/**
 * Select component highlighting result item.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * Fields array.
     *
     * @var array
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @param array $fields
     */
    public function __construct($fields)
    {
        $this->fields = $fields;
    }

    /**
     * Get highlights for all fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get highlights for a single field.
     *
     * @param string $key
     *
     * @return array
     */
    public function getField($key)
    {
        if (isset($this->fields[$key])) {
            return $this->fields[$key];
        }

        return [];
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->fields);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->fields);
    }
}
