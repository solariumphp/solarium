<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\DeleteFieldType;
use Solarium\QueryType\Schema\Query\FieldType\FieldType;
use Solarium\QueryType\Schema\Query\Query;

class DeleteFieldTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DeleteFieldType
     */
    private $command;

    protected function setUp()
    {
        $this->command = new DeleteFieldType();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_DELETE_FIELD_TYPE, $this->command->getType());
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
                ),
                array(
                    'name' => 'name2',
                ),
                array(
                    'name' => 'name3',
                ),
                array(
                    'name' => 'name4',
                ),
                array(
                    'name' => 'name5',
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
