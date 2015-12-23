<?php

namespace Solarium\Tests\QueryType\Schema\Field;

use Solarium\QueryType\Schema\Query\Field\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Field
     */
    private $field;

    protected function setUp()
    {
        $this->field = new Field();
    }

    public function testIsField()
    {
        $this->assertInstanceOf('Solarium\QueryType\Schema\Query\Field\FieldInterface', $this->field);
    }

    public function testSetAndGetDefault()
    {
        $this->assertNull($this->field->getDefault());
        $default = 'defaultValue';
        $this->field->setDefault($default);
        $this->assertEquals($default, $this->field->getDefault());
    }

    public function testIsAndSetIndexed()
    {
        $this->assertNull($this->field->isIndexed());
        $this->field->setIndexed(1);
        $this->assertTrue($this->field->isIndexed());
    }

    public function testIsAndSetMultiValued()
    {
        $this->assertNull($this->field->isMultiValued());
        $this->field->setMultiValued(1);
        $this->assertTrue($this->field->isMultiValued());
    }

    public function testSetAndGetName()
    {
        $this->assertNull($this->field->getName());
        $name = 'this_is_a_name';
        $this->field->setName($name);
        $this->assertEquals($name, $this->field->getName());
        $this->assertEquals($name, (string) $this->field);
    }

    public function testIsAndGetRequired()
    {
        $this->assertNull($this->field->isRequired());
        $this->field->setRequired(1);
        $this->assertTrue($this->field->isRequired());
    }

    public function testIsAndSetStored()
    {
        $this->assertNull($this->field->isStored());
        $this->field->setStored(1);
        $this->assertTrue($this->field->isStored());
    }

    public function testSetAndGetType()
    {
        $this->assertNull($this->field->getType());
        $type = 'testType';
        $this->field->setType($type);
        $this->assertEquals($type, $this->field->getType());
    }

    public function testCastAsArray()
    {
        $field = new Field(array(
            'name' => 'attribute',
            'required' => 1,
            'ignored' => 0,
            'multiValued' => true,
        ));
        $field->setDefault('defaultValue');
        $field->setType('string');
        $this->assertEquals(
            array(
                'name' => 'attribute',
                'type' => 'string',
                'default' => 'defaultValue',
                'required' => true,
                'multiValued' => true,
            ),
            $field->castAsArray()
        );
    }
}
