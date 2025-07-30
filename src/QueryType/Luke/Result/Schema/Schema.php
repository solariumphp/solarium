<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema;

use Solarium\QueryType\Luke\Result\Schema\Field\DynamicField;
use Solarium\QueryType\Luke\Result\Schema\Field\Field;
use Solarium\QueryType\Luke\Result\Schema\Type\Type;

/**
 * Retrieved schema information.
 */
class Schema
{
    /**
     * @var Field[]
     */
    protected $fields;

    /**
     * @var DynamicField[]
     */
    protected $dynamicFields;

    /**
     * @var Field|null
     */
    protected $uniqueKeyField = null;

    /**
     * @var Similarity
     */
    protected $similarity;

    /**
     * @var Type[]
     */
    protected $types;

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param string $name
     *
     * @return Field|null
     */
    public function getField(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * @param Field[] $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return DynamicField[]
     */
    public function getDynamicFields(): array
    {
        return $this->dynamicFields;
    }

    /**
     * @param string $name
     *
     * @return DynamicField|null
     */
    public function getDynamicField(string $name): ?DynamicField
    {
        return $this->dynamicFields[$name] ?? null;
    }

    /**
     * @param DynamicField[] $dynamicFields
     *
     * @return self Provides fluent interface
     */
    public function setDynamicFields(array $dynamicFields): self
    {
        $this->dynamicFields = $dynamicFields;

        return $this;
    }

    /**
     * @return Field|null
     */
    public function &getUniqueKeyField(): ?Field
    {
        return $this->uniqueKeyField;
    }

    /**
     * @param Field $uniqueKeyField
     *
     * @return self Provides fluent interface
     */
    public function setUniqueKeyField(Field &$uniqueKeyField): self
    {
        $this->uniqueKeyField = &$uniqueKeyField;

        return $this;
    }

    /**
     * @return Similarity
     */
    public function getSimilarity(): Similarity
    {
        return $this->similarity;
    }

    /**
     * @param Similarity $similarity
     *
     * @return self Provides fluent interface
     */
    public function setSimilarity(Similarity $similarity): self
    {
        $this->similarity = $similarity;

        return $this;
    }

    /**
     * @return Type[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * @param string $name
     *
     * @return Type|null
     */
    public function getType(string $name): ?Type
    {
        return $this->types[$name] ?? null;
    }

    /**
     * @param Type[] $types
     *
     * @return self Provides fluent interface
     */
    public function setTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }
}
