<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Select\Result;

use Solarium\Core\Query\AbstractDocument;
use Solarium\Exception\RuntimeException;

/**
 * Read-only Solr document.
 *
 * This is the default Solr document type returned by a select query. You can
 * access the fields as object properties or iterate over all fields.
 */
class Document extends AbstractDocument
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
     * @param string $name
     * @param string $value
     *
     * @throws RuntimeException
     */
    public function __set($name, $value): void
    {
        throw new RuntimeException('A readonly document cannot be altered');
    }

    #[\ReturnTypeWillChange]
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->getFields();
    }
}
