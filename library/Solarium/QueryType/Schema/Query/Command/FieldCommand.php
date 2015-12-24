<?php

namespace Solarium\QueryType\Schema\Query\Command;

use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Schema\Query\Field\Field;
use Solarium\QueryType\Schema\Query\Field\FieldInterface;

abstract class FieldCommand extends Command
{
    /**
     * @var FieldInterface[]|Field[]
     */
    protected $fields = array();

    /**
     * @return Field[]|FieldInterface[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Field[]|FieldInterface[] $fields
     * @return $this - Provides Fluent Interface
     */
    public function setFields(array $fields)
    {
        $this->fields = array();
        $this->addFields($fields);

        return $this;
    }

    /**
     * @param Field[]|FieldInterface[] $fields
     * @return $this - Provides Fluent Interface
     */
    public function addFields(array $fields)
    {
        foreach ($fields AS $field) {
            (is_array($field)) ? $this->createField($field) : $this->addField($field);
        }

        return $this;
    }

    /**
     * @param FieldInterface $field
     * @return $this
     */
    public function addField(FieldInterface $field)
    {
        if (!array_key_exists((string) $field, $this->getFields())) {
            $this->fields[(string) $field] = $field;
        }

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function createField(array $attributes = array())
    {
        if (!array_key_exists('name', $attributes)) {
            throw new RuntimeException("A field must have a name attribute.");
        }
        $field = new Field($attributes);
        $this->addField($field);

        return $field;
    }
}
