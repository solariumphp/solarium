<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\AddCopyField;
use Solarium\QueryType\Schema\Query\Field\CopyField;
use Solarium\QueryType\Schema\Query\Query;

class AddCopyFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AddCopyField
     */
    protected $command;

    protected function setUp()
    {
        $this->command = new AddCopyField();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_ADD_COPY_FIELD, $this->command->getType());
    }

    public function testSetAndCastAsArray()
    {
        $this->command->setFields(array(new CopyField('source1', 'dest1'), new CopyField('source2', 'dest2')));
        $this->command->addFields(array(new CopyField('source3', 'dest3')));
        $this->command->addField(new CopyField('source4', 'dest4'));
        $this->assertCount(4, $this->command->getFields());
        $this->assertEquals(
            array(
                array(
                    'source' => 'source1',
                    'dest' => array('dest1'),
                ),
                array(
                    'source' => 'source2',
                    'dest' => array('dest2'),
                ),
                array(
                    'source' => 'source3',
                    'dest' => array('dest3'),
                ),
                array(
                    'source' => 'source4',
                    'dest' => array('dest4'),
                ),
            ),
            $this->command->castAsArray()
        );
    }
}
