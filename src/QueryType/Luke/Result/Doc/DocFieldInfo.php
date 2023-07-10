<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Doc;

use Solarium\QueryType\Luke\Result\FlagList;

/**
 * Document field information.
 */
class DocFieldInfo
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
     * @var FlagList
     */
    protected $flags;

    /**
     * @var string|null
     */
    protected $value = null;

    /**
     * @var string|null
     */
    protected $internal;

    /**
     * @var string|null
     */
    protected $binary;

    /**
     * @var int|null
     */
    protected $docFreq;

    /**
     * @var array|null
     */
    protected $termVector;

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
     * Returns index flags.
     *
     * @return FlagList
     */
    public function getFlags(): FlagList
    {
        return $this->flags;
    }

    /**
     * @param FlagList $flags
     *
     * @return self Provides fluent interface
     */
    public function setFlags($flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * Returns the external (human readable) value.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     *
     * @return self Provides fluent interface
     */
    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns the stored value.
     *
     * @return string|null
     */
    public function getInternal(): ?string
    {
        return $this->internal;
    }

    /**
     * @param string|null $internal
     *
     * @return self Provides fluent interface
     */
    public function setInternal(?string $internal): self
    {
        $this->internal = $internal;

        return $this;
    }

    /**
     * Returns the binary value.
     *
     * @return string|null
     */
    public function getBinary(): ?string
    {
        return $this->binary;
    }

    /**
     * @param string|null $binary
     *
     * @return self Provides fluent interface
     */
    public function setBinary(?string $binary): self
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * Returns the number of documents that contain the term in the field.
     *
     * This can be 0 for non-indexed fields or null because it isn't calculated for point fields.
     *
     * @return int|null
     */
    public function getDocFreq(): ?int
    {
        return $this->docFreq;
    }

    /**
     * @param int|null $docFreq
     *
     * @return self Provides fluent interface
     */
    public function setDocFreq(?int $docFreq): self
    {
        $this->docFreq = $docFreq;

        return $this;
    }

    /**
     * Returns the term vector.
     *
     * @return array|null
     */
    public function getTermVector(): ?array
    {
        return $this->termVector;
    }

    /**
     * @param array|null $termVector
     *
     * @return self Provides fluent interface
     */
    public function setTermVector(?array $termVector): self
    {
        $this->termVector = $termVector;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name.': '.$this->value;
    }
}
