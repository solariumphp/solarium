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

class Solarium_Query_UpdateTest extends PHPUnit_Framework_TestCase
{

    protected $_query;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Update;
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Client::QUERYTYPE_UPDATE, $this->_query->getType());
    }

    public function testConfigMode()
    {
        $options = array(
            'handler'  => 'myHandler',
            'resultclass' => 'myResult',
            'command' => array(
                'key1' => array(
                    'type' => 'delete',
                    'query' => 'population:[* TO 1000]',
                    'id' => array(1,2),
                ),
                'key2' => array(
                    'type' => 'commit',
                    'waitflush' => true,
                    'waitsearcher' => false,
                    'expungedeletes' => true,
                ),
                'key3' => array(
                    'type' => 'optimize',
                    'waitflush' => true,
                    'waitsearcher' => false,
                    'maxsegments' => 5,
                ),
                'key4' => array(
                    'type' => 'rollback',
                )
            )
        );
        $this->_query->setOptions($options);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            $options['handler'],
            $this->_query->getHandler()
        );

        $this->assertEquals(
            $options['resultclass'],
            $this->_query->getResultClass()
        );

        $delete = $commands['key1'];
        $this->assertEquals(
            array(1,2),
            $delete->getIds()
        );
        $this->assertEquals(
            array('population:[* TO 1000]'),
            $delete->getQueries()
        );

        $commit = $commands['key2'];
        $this->assertEquals(
            true,
            $commit->getWaitFlush()
        );
        $this->assertEquals(
            false,
            $commit->getWaitSearcher()
        );
        $this->assertEquals(
            true,
            $commit->getExpungeDeletes()
        );

        $optimize = $commands['key3'];
        $this->assertEquals(
            true,
            $optimize->getWaitFlush()
        );
        $this->assertEquals(
            false,
            $optimize->getWaitSearcher()
        );
        $this->assertEquals(
            5,
            $optimize->getMaxSegments()
        );

        $rollback = $commands['key4'];
        $this->assertEquals(
            'Solarium_Query_Update_Command_Rollback',
            get_class($rollback)
        );
    }

    public function testConstructorWithConfigAddCommand()
    {
        $config = array(
            'command' => array(
                'key1' => array(
                    'type' => 'add',
                ),
            )
        );

        $this->setExpectedException('Solarium_Exception');
        new Solarium_Query_Update($config);
    }

    public function testAddWithoutKey()
    {
        $command = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add(null, $command);

        $this->assertEquals(
            array($command),
            $this->_query->getCommands()
        );
    }
    
    public function testAddWithKey()
    {
        $rollback = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add('rb', $rollback);

        $commit = new Solarium_Query_Update_Command_Commit;
        $this->_query->add('cm', $commit);

        $this->assertEquals(
            array('rb' => $rollback, 'cm' => $commit),
            $this->_query->getCommands()
        );
    }

    public function testRemove()
    {
        $rollback = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add('rb', $rollback);

        $commit = new Solarium_Query_Update_Command_Commit;
        $this->_query->add('cm', $commit);
        
        $this->_query->remove('rb');

        $this->assertEquals(
            array('cm' => $commit),
            $this->_query->getCommands()
        );
    }

    public function testRemoveWithObjectInput()
    {
        $rollback = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add('rb', $rollback);

        $commit = new Solarium_Query_Update_Command_Commit;
        $this->_query->add('cm', $commit);

        $this->_query->remove($rollback);

        $this->assertEquals(
            array('cm' => $commit),
            $this->_query->getCommands()
        );
    }

    public function testRemoveInvalidKey()
    {
        $rollback = new Solarium_Query_Update_Command_Rollback;
        $this->_query->add('rb', $rollback);

        $commit = new Solarium_Query_Update_Command_Commit;
        $this->_query->add('cm', $commit);

        $this->_query->remove('invalidkey'); //should silently ignore

        $this->assertEquals(
            array('rb' => $rollback, 'cm' => $commit),
            $this->_query->getCommands()
        );
    }

    public function testAddRollback()
    {
        $this->_query->addRollback();
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_ROLLBACK,
            $commands[0]->getType()
        );
    }

    public function testAddDeleteQuery()
    {
        $this->_query->addDeleteQuery('*:*');
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array('*:*'),
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteQueries()
    {
        $this->_query->addDeleteQueries(array('id:1','id:2'));
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array('id:1','id:2'),
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteById()
    {
        $this->_query->addDeleteById(1);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array(1),
            $commands[0]->getIds()
        );
    }

    public function testAddDeleteByIds()
    {
        $this->_query->addDeleteByIds(array(1,2));
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array(1,2),
            $commands[0]->getIds()
        );
    }

    public function testAddDocument()
    {
        $doc = new Solarium_Document_ReadWrite(array('id' => 1));

        $this->_query->addDocument($doc);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_ADD,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array($doc),
            $commands[0]->getDocuments()
        );
    }

    public function testAddDocuments()
    {
        $doc1 = new Solarium_Document_ReadWrite(array('id' => 1));
        $doc2 = new Solarium_Document_ReadWrite(array('id' => 1));

        $this->_query->addDocuments(array($doc1,$doc2), true, 100);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_ADD,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array($doc1, $doc2),
            $commands[0]->getDocuments()
        );

        $this->assertEquals(
            true,
            $commands[0]->getOverwrite()
        );

        $this->assertEquals(
            100,
            $commands[0]->getCommitWithin()
        );
    }

    public function testAddCommit()
    {
        $this->_query->addCommit(true, false, true);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_COMMIT,
            $commands[0]->getType()
        );

        $this->assertEquals(
            true,
            $commands[0]->getWaitFlush()
        );

        $this->assertEquals(
            false,
            $commands[0]->getWaitSearcher()
        );

        $this->assertEquals(
            true,
            $commands[0]->getExpungeDeletes()
        );
    }

    public function testAddOptimize()
    {
        $this->_query->addOptimize(true, false, 10);
        $commands = $this->_query->getCommands();

        $this->assertEquals(
            Solarium_Query_Update::COMMAND_OPTIMIZE,
            Solarium_Query_Update::COMMAND_OPTIMIZE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            true,
            $commands[0]->getWaitFlush()
        );

        $this->assertEquals(
            false,
            $commands[0]->getWaitSearcher()
        );

        $this->assertEquals(
            10,
            $commands[0]->getMaxSegments()
        );
    }

    public function testCreateCommand()
    {
        $type = Solarium_Query_Update::COMMAND_ROLLBACK;
        $options = array('optionA' => 1, 'optionB' => 2);
        $command = $this->_query->createCommand($type, $options);

        // check command type
        $this->assertEquals(
            $type,
            $command->getType()
        );

        // check option forwarding
        $commandOptions = $command->getOptions();
        $this->assertEquals(
            $options['optionB'],
            $commandOptions['optionB']
        );
    }

    public function testCreateCommandWithInvalidQueryType()
    {
        $this->setExpectedException('Solarium_Exception');
        $this->_query->createCommand('invalidtype');
    }

    public function testSetAndGetDocumentClass()
    {
        $this->_query->setDocumentClass('MyDocument');
        $this->assertEquals('MyDocument', $this->_query->getDocumentClass());
    }

    public function testCreateDocument()
    {
        $doc = $this->_query->createDocument();
        $this->assertThat($doc, $this->isInstanceOf($this->_query->getDocumentClass()));
    }

    public function testCreateDocumentWithCustomClass()
    {
        $this->_query->setDocumentClass('MyCustomDoc');

        $doc = $this->_query->createDocument();
        $this->assertThat($doc, $this->isInstanceOf('MyCustomDoc'));
    }

    public function testCreateDocumentWithFieldsAndBoosts()
    {
        $fields = array('id' => 1, 'name' => 'testname');
        $boosts = array('name' => 2.7);

        $doc = $this->_query->createDocument($fields, $boosts);

        $this->assertThat($doc, $this->isInstanceOf($this->_query->getDocumentClass()));

        $this->assertEquals(
            $fields,
            $doc->getFields()
        );

        $this->assertEquals(
            2.7,
            $doc->getFieldBoost('name')
        );
    }

}

class MyCustomDoc extends Solarium_Document_ReadWrite{

}
