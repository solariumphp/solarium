<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Field;

use Solarium\QueryType\Luke\Result\FlagList;
use Solarium\QueryType\Luke\Result\Schema\Type\Type;

/**
 * Field base class.
 */
abstract class AbstractField implements FieldInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var FlagList
     */
    protected $flags;

    /**
     * @var bool|null
     */
    protected $required = null;

    /**
     * @var string|null
     */
    protected $default = null;

    /**
     * @var bool|null
     */
    protected $uniqueKey = null;

    /**
     * @var int|null
     */
    protected $positionIncrementGap = null;

    /**
     * @var CopyFieldDestInterface[]
     */
    protected $copyDests = [];

    /**
     * @var CopyFieldSourceInterface[]
     */
    protected $copySources = [];

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
     * @return Type
     */
    public function &getType(): Type
    {
        return $this->type;
    }

    /**
     * @param Type $type
     *
     * @return self
     */
    public function setType(Type &$type): self
    {
        $this->type = &$type;

        return $this;
    }

    /**
     * @return FlagList
     */
    public function getFlags(): FlagList
    {
        return $this->flags;
    }

    /**
     * @param FlagList $flags
     *
     * @return self
     */
    public function setFlags(FlagList $flags): self
    {
        $this->flags = $flags;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @param bool|null $required
     *
     * @return self
     */
    public function setRequired(?bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return true === $this->required;
    }

    /**
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @param string|null $default
     *
     * @return self
     */
    public function setDefault(?string $default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getUniqueKey(): ?bool
    {
        return $this->uniqueKey;
    }

    /**
     * @param bool|null $uniqueKey
     *
     * @return self
     */
    public function setUniqueKey(?bool $uniqueKey): self
    {
        $this->uniqueKey = $uniqueKey;

        return $this;
    }

    /**
     * @return bool
     */
    public function isUniqueKey(): bool
    {
        return true === $this->uniqueKey;
    }

    /**
     * @return int|null
     */
    public function getPositionIncrementGap(): ?int
    {
        return $this->positionIncrementGap;
    }

    /**
     * @param int|null $positionIncrementGap
     *
     * @return self
     */
    public function setPositionIncrementGap(?int $positionIncrementGap): self
    {
        $this->positionIncrementGap = $positionIncrementGap;

        return $this;
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
     * @return self
     */
    public function addCopyDest(CopyFieldDestInterface &$copyDest): self
    {
        $this->copyDests[] = &$copyDest;

        return $this;
    }

    /**
     * @return CopyFieldSourceInterface[]
     */
    public function getCopySources(): array
    {
        return $this->copySources;
    }

    /**
     * @param CopyFieldSourceInterface $copySource
     *
     * @return self
     */
    public function addCopySource(CopyFieldSourceInterface &$copySource): self
    {
        $this->copySources[] = &$copySource;

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
