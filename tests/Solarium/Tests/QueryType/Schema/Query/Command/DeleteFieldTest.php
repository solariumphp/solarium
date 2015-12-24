<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\DeleteField;
use Solarium\QueryType\Schema\Query\Field\Field;
use Solarium\QueryType\Schema\Query\Query;

class DeleteFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DeleteField
     */
    protected $command;

    protected function setUp()
    {
        $this->command = new DeleteField();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_DELETE_FIELD, $this->command->getType());
    }

    public function testSetAndCastAsArray()
    {
        $this->command->setFields(array($this->createNewFieldWithNumberedValues(1), $this->createNewFieldWithNumberedValues(2)));
        $this->command->addFields(array($this->createNewFieldWithNumberedValues(3)));
        $this->command->addField($this->createNewFieldWithNumberedValues(4));
        $this->assertCount(4, $this->command->getFields());
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
            ),
            $this->command->castAsArray()
        );
    }

    protected function createNewFieldWithNumberedValues($number)
    {
        return new Field(array(
            'name' => 'name' . $number,
            'type' => 'type' . $number,
        ));
    }
}
