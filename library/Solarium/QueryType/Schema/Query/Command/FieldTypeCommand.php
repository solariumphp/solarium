<?php

namespace Solarium\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\FieldType\FieldType;
use Solarium\QueryType\Schema\Query\FieldType\FieldTypeInterface;

abstract class FieldTypeCommand extends Command
{
    /**
     * @var FieldTypeInterface[]
     */
    protected $fieldTypes = array();

    /**
     * @return FieldTypeInterface[]
     */
    public function getFieldTypes()
    {
        return $this->fieldTypes;
    }

    /**
     * @param FieldTypeInterface[] $fieldTypes
     * @return $this - Provides Fluent Interface
     */
    public function setFieldTypes(array $fieldTypes)
    {
        $this->fieldTypes = array();
        $this->addFieldTypes($fieldTypes);

        return $this;
    }

    /**
     * @param FieldTypeInterface $fieldType
     * @return $this
     */
    public function addFieldType(FieldTypeInterface $fieldType)
    {
        $this->fieldTypes[] = $fieldType;

        return $this;
    }

    /**
     * @param FieldTypeInterface[] $fieldTypes
     * @return $this - Provides Fluent Interface
     */
    public function addFieldTypes(array $fieldTypes)
    {
        foreach ($fieldTypes AS $fieldType) {
            $this->addFieldType($fieldType);
        }

        return $this;
    }

    /**
     * @param null $name
     * @param null $class
     * @return FieldType
     */
    public function createFieldType($name = null, $class = null)
    {
        $fieldType = new FieldType($name, $class);
        $this->addFieldType($fieldType);

        return $fieldType;
    }
}
