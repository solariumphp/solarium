<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\AddFieldType;
use Solarium\QueryType\Schema\Query\FieldType\FieldType;
use Solarium\QueryType\Schema\Query\Query;

class AddFieldTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddFieldType
     */
    protected $command;

    protected function setUp()
    {
        $this->command = new AddFieldType();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_ADD_FIELD_TYPE, $this->command->getType());
    }

    public function testSetAndCastAsArray()
    {
        $this->command->setFieldTypes(array($this->createNewFieldTypeWithNumberedValues(1), $this->createNewFieldTypeWithNumberedValues(2)));
        $this->command->addFieldTypes(array($this->createNewFieldTypeWithNumberedValues(3)));
        $this->command->createFieldType('name4', 'class4');
        $this->command->addFieldType($this->createNewFieldTypeWithNumberedValues(5));
        $this->assertCount(5, $this->command->getFieldTypes());
        $this->assertEquals(
            array(
                array(
                    'name' => 'name1',
                    'class' => 'class1',
                ),
                array(
                    'name' => 'name2',
                    'class' => 'class2',
                ),
                array(
                    'name' => 'name3',
                    'class' => 'class3',
                ),
                array(
                    'name' => 'name4',
                    'class' => 'class4',
                ),
                array(
                    'name' => 'name5',
                    'class' => 'class5',
                ),
            ),
            $this->command->castAsArray()
        );
    }

    protected function createNewFieldTypeWithNumberedValues($number)
    {
        return new FieldType('name' . $number, 'class' . $number);
    }
}
