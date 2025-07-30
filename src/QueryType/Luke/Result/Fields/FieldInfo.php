<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Fields;

use Solarium\QueryType\Luke\Result\FlagList;

/**
 * Retrieved field information.
 */
class FieldInfo
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var FlagList
     */
    protected $schema;

    /**
     * @var string|null
     */
    protected $dynamicBase = null;

    /**
     * @var FlagList|string|null
     */
    protected $index = null;

    /**
     * @var int|null
     */
    protected $docs = null;

    /**
     * @var int|null
     */
    protected $distinct = null;

    /**
     * @var array|null
     */
    protected $topTerms = null;

    /**
     * @var array|null
     */
    protected $histogram = null;

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
     * Returns field name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns field type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return self Provides fluent interface
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Returns schema flags.
     *
     * @return FlagList
     */
    public function getSchema(): FlagList
    {
        return $this->schema;
    }

    /**
     * @param FlagList $schema
     *
     * @return self Provides fluent interface
     */
    public function setSchema(FlagList $schema): self
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Returns the name of the dynamic field this field is based on.
     *
     * @return string|null
     */
    public function getDynamicBase(): ?string
    {
        return $this->dynamicBase;
    }

    /**
     * @param string|null $dynamicBase
     *
     * @return self Provides fluent interface
     */
    public function setDynamicBase(?string $dynamicBase): self
    {
        $this->dynamicBase = $dynamicBase;

        return $this;
    }

    /**
     * Returns index flags.
     *
     * @return FlagList|string|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param FlagList|string|null $index
     *
     * @return self Provides fluent interface
     */
    public function setIndex($index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Returns number of documents.
     *
     * @return int|null
     */
    public function getDocs(): ?int
    {
        return $this->docs;
    }

    /**
     * @param int|null $docs
     *
     * @return self Provides fluent interface
     */
    public function setDocs(?int $docs): self
    {
        $this->docs = $docs;

        return $this;
    }

    /**
     * Returns number of distinct terms.
     *
     * @return int|null
     */
    public function getDistinct(): ?int
    {
        return $this->distinct;
    }

    /**
     * @param int|null $distinct
     *
     * @return self Provides fluent interface
     */
    public function setDistinct(?int $distinct): self
    {
        $this->distinct = $distinct;

        return $this;
    }

    /**
     * Returns the list of top terms and their document frequencies.
     *
     * @return array|null
     */
    public function getTopTerms(): ?array
    {
        return $this->topTerms;
    }

    /**
     * @param array|null $topTerms
     *
     * @return self Provides fluent interface
     */
    public function setTopTerms(?array $topTerms): self
    {
        $this->topTerms = $topTerms;

        return $this;
    }

    /**
     * Returns the term histogram.
     *
     * @return array|null
     */
    public function getHistogram(): ?array
    {
        return $this->histogram;
    }

    /**
     * @param array|null $histogram
     *
     * @return self Provides fluent interface
     */
    public function setHistogram(?array $histogram): self
    {
        $this->histogram = $histogram;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
