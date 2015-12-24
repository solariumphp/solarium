<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Schema\Query;

use Solarium\Core\Client\Client;
use Solarium\QueryType\Schema\Query\Command\AddField;
use Solarium\QueryType\Schema\Query\Command\DeleteField;
use Solarium\QueryType\Schema\Query\Field\CopyField;
use Solarium\QueryType\Schema\Query\Field\Field;
use Solarium\QueryType\Schema\Query\FieldType\FieldType;
use Solarium\QueryType\Schema\Query\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    private $query;

    protected function setUp()
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertEquals(Client::QUERY_SCHEMA, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\ResponseParser',
            $this->query->getResponseParser()
        );
    }

    public function testGetResponseBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\RequestBuilder',
            $this->query->getRequestBuilder()
        );
    }

    public function testAddAndRemoveCommand()
    {
        $command = new AddField();
        $this->assertCount(0, $this->query->getCommands());
        $this->query->add('command_key', $command);
        $this->assertCount(1, $this->query->getCommands());
        $this->query->remove(new DeleteField());//try to remove a command the query does not have
        $this->assertCount(1, $this->query->getCommands());
        $this->query->remove($command);//now take off the original command
        $this->assertCount(0, $this->query->getCommands());
    }

    public function testCreateCommandWithKnownType()
    {
        $type = Query::COMMAND_REPLACE_DYNAMIC_FIELD;
        $options = array('this' => 'that');
        $command = $this->query->createCommand($type, $options);
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\ReplaceDynamicField',
            $command
        );
        $this->assertSame($options, $command->getOptions());
    }

    public function testCreateCommandWithUnknownType()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->query->createCommand('unknown', array());
    }

    public function testHasCommands()
    {
        $this->assertFalse($this->query->hasCommands());
        $this->query->add('added_command', new AddField());
        $this->assertTrue($this->query->hasCommands());
    }

    public function testAddFields()
    {
        $field = $this->getTestField();
        $this->query->addFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testAddField()
    {
        $field = $this->getTestField();
        $this->query->addField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testReplaceFields()
    {
        $field = $this->getTestField();
        $this->query->replaceFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_REPLACE_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_REPLACE_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\ReplaceField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testReplaceField()
    {
        $field = $this->getTestField();
        $this->query->replaceField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_REPLACE_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_REPLACE_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\ReplaceField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testDeleteFields()
    {
        $field = $this->getTestField();
        $this->query->deleteFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testDeleteField()
    {
        $field = $this->getTestField();
        $this->query->deleteField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testAddDynamicFields()
    {
        $field = $this->getTestField();
        $this->query->addDynamicFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_DYNAMIC_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_DYNAMIC_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddDynamicField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testAddDynamicField()
    {
        $field = $this->getTestField();
        $this->query->addDynamicField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_DYNAMIC_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_DYNAMIC_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddDynamicField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testReplaceDynamicFields()
    {
        $field = $this->getTestField();
        $this->query->replaceDynamicFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_REPLACE_DYNAMIC_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_REPLACE_DYNAMIC_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\ReplaceDynamicField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testReplaceDynamicField()
    {
        $field = $this->getTestField();
        $this->query->replaceDynamicField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_REPLACE_DYNAMIC_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_REPLACE_DYNAMIC_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\ReplaceDynamicField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testDeleteDynamicFields()
    {
        $field = $this->getTestField();
        $this->query->deleteDynamicFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_DYNAMIC_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_DYNAMIC_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteDynamicField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testDeleteDynamicField()
    {
        $field = $this->getTestField();
        $this->query->deleteDynamicField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_DYNAMIC_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_DYNAMIC_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteDynamicField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testAddCopyFields()
    {
        $field = $this->getTestCopyField();
        $this->query->addCopyFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_COPY_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_COPY_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddCopyField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testAddCopyField()
    {
        $field = $this->getTestCopyField();
        $this->query->addCopyField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_COPY_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_COPY_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddCopyField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testDeleteCopyFields()
    {
        $field = $this->getTestCopyField();
        $this->query->deleteCopyFields(array($field));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_COPY_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_COPY_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteCopyField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testDeleteCopyField()
    {
        $field = $this->getTestCopyField();
        $this->query->deleteCopyField($field);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_COPY_FIELD]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_COPY_FIELD];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteCopyField',
            $command
        );
        $fields = array_values($command->getFields());
        $this->assertSame($field, $fields[0]);
    }

    public function testAddFieldTypes()
    {
        $fieldType = $this->getTestFieldType();
        $this->query->addFieldTypes(array($fieldType));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_FIELD_TYPE]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_FIELD_TYPE];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddFieldType',
            $command
        );
        $fieldTypes = array_values($command->getFieldTypes());
        $this->assertSame($fieldType, $fieldTypes[0]);
    }

    public function testAddFieldType()
    {
        $fieldType = $this->getTestFieldType();
        $this->query->addFieldType($fieldType);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_ADD_FIELD_TYPE]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_ADD_FIELD_TYPE];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\AddFieldType',
            $command
        );
        $fieldTypes = array_values($command->getFieldTypes());
        $this->assertSame($fieldType, $fieldTypes[0]);
    }

    public function testReplaceFieldTypes()
    {
        $fieldType = $this->getTestFieldType();
        $this->query->replaceFieldTypes(array($fieldType));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_REPLACE_FIELD_TYPE]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_REPLACE_FIELD_TYPE];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\ReplaceFieldType',
            $command
        );
        $fieldTypes = array_values($command->getFieldTypes());
        $this->assertSame($fieldType, $fieldTypes[0]);
    }

    public function testReplaceFieldType()
    {
        $fieldType = $this->getTestFieldType();
        $this->query->replaceFieldType($fieldType);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_REPLACE_FIELD_TYPE]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_REPLACE_FIELD_TYPE];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\ReplaceFieldType',
            $command
        );
        $fieldTypes = array_values($command->getFieldTypes());
        $this->assertSame($fieldType, $fieldTypes[0]);
    }

    public function testDeleteFieldTypes()
    {
        $fieldType = $this->getTestFieldType();
        $this->query->deleteFieldTypes(array($fieldType));
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_FIELD_TYPE]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_FIELD_TYPE];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteFieldType',
            $command
        );
        $fieldTypes = array_values($command->getFieldTypes());
        $this->assertSame($fieldType, $fieldTypes[0]);
    }

    public function testDeleteFieldType()
    {
        $fieldType = $this->getTestFieldType();
        $this->query->deleteFieldType($fieldType);
        $commands = $this->query->getCommands();
        $this->assertTrue(isset($commands[Query::COMMAND_DELETE_FIELD_TYPE]));
        $this->assertCount(1, $commands);
        $command = $commands[Query::COMMAND_DELETE_FIELD_TYPE];
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\Command\DeleteFieldType',
            $command
        );
        $fieldTypes = array_values($command->getFieldTypes());
        $this->assertSame($fieldType, $fieldTypes[0]);
    }

    public function testCastAsArray()
    {
        $this->assertEquals(array(), $this->query->castAsArray());
        $fieldArray = array('this' => 'and that');
        $field = $this->getMock('Solarium\QueryType\Schema\Query\Field\FieldInterface');
        $field
            ->expects($this->any())
            ->method('castAsArray')
            ->will($this->returnValue($fieldArray));
        $field
            ->expects($this->any())
            ->method('__toString')
            ->will($this->returnValue('name'));
        $this->query->addField($field);
        $this->assertEquals(array(Query::COMMAND_ADD_FIELD => array($fieldArray)), $this->query->castAsArray());
    }

    private function getTestField()
    {
        return new Field(array('name' => 'field', 'type' => 'string'));
    }

    private function getTestCopyField()
    {
        return new CopyField('source', 'dest');
    }

    private function getTestFieldType()
    {
        return new FieldType('ftype');
    }
}
