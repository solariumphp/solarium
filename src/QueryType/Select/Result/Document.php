<?php

namespace Solarium\QueryType\Select\Result;

use Solarium\Exception\RuntimeException;

/**
 * Read-only Solr document.
 *
 * This is the default Solr document type returned by a select query. You can
 * access the fields as object properties or iterate over all fields.
 */
class Document extends AbstractDocument implements DocumentInterface
{
    /**
     * All fields in this document.
     *
     * @var array
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Set field value.
     *
     * Magic method for setting a field as property of this object. Since this
     * is a readonly document an exception will be thrown to prevent this.
     *
     *
     * @param string $name
     * @param string $value
     *
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        throw new RuntimeException('A readonly document cannot be altered');
    }
}
