<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Field;

/**
 * Wildcard field used as a copy source.
 *
 * This is used when a copyField source isn't an explicit field and doesn't match a dynamicField.
 *
 * @internal a copyField destination can only be an explicit field or match a dynamicField
 *
 * @see https://solr.apache.org/guide/copying-fields.html
 */
class WildcardField implements CopyFieldSourceInterface, FieldInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var CopyFieldDestInterface[]
     */
    protected $copyDests = [];

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CopyFieldDestInterface[]
     */
    public function getCopyDests(): array
    {
        return $this->copyDests;
    }

    /**
     * @param CopyFieldDestInterface $copyDest
     *
     * @return self Provides fluent interface
     */
    public function addCopyDest(CopyFieldDestInterface &$copyDest): self
    {
        $this->copyDests[] = &$copyDest;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
