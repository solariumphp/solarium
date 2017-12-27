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

namespace Solarium\Tests\QueryType\Update\Query;

use Solarium\Core\Client\Client;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Query\Command\Rollback;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Document\Document;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    protected $query;

    public function setUp()
    {
        $this->query = new Query;
    }

    public function testGetType()
    {
        $this->assertEquals(Client::QUERY_UPDATE, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Update\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Update\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testConfigMode()
    {
        $options = array(
            'handler' => 'myHandler',
            'resultclass' => 'myResult',
            'command' => array(
                'key1' => array(
                    'type' => 'delete',
                    'query' => 'population:[* TO 1000]',
                    'id' => array(1, 2),
                ),
                'key2' => array(
                    'type' => 'commit',
                    'softcommit' => true,
                    'waitsearcher' => false,
                    'expungedeletes' => true,
                ),
                'key3' => array(
                    'type' => 'optimize',
                    'softcommit' => true,
                    'waitsearcher' => false,
                    'maxsegments' => 5,
                ),
                'key4' => array(
                    'type' => 'rollback',
                )
            )
        );
        $this->query->setOptions($options);
        $commands = $this->query->getCommands();

        $this->assertEquals(
            $options['handler'],
            $this->query->getHandler()
        );

        $this->assertEquals(
            $options['resultclass'],
            $this->query->getResultClass()
        );

        $delete = $commands['key1'];
        $this->assertEquals(
            array(1, 2),
            $delete->getIds()
        );
        $this->assertEquals(
            array('population:[* TO 1000]'),
            $delete->getQueries()
        );

        $commit = $commands['key2'];
        $this->assertEquals(
            true,
            $commit->getSoftCommit()
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
            $optimize->getSoftCommit()
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
            'Solarium\QueryType\Update\Query\Command\Rollback',
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

        $this->setExpectedException('Solarium\Exception\RuntimeException');
        new Query($config);
    }

    public function testAddWithoutKey()
    {
        $command = new Rollback;
        $this->query->add(null, $command);

        $this->assertEquals(
            array($command),
            $this->query->getCommands()
        );
    }

    public function testAddWithKey()
    {
        $rollback = new Rollback;
        $this->query->add('rb', $rollback);

        $commit = new Commit;
        $this->query->add('cm', $commit);

        $this->assertEquals(
            array('rb' => $rollback, 'cm' => $commit),
            $this->query->getCommands()
        );
    }

    public function testRemove()
    {
        $rollback = new Rollback;
        $this->query->add('rb', $rollback);

        $commit = new Commit;
        $this->query->add('cm', $commit);

        $this->query->remove('rb');

        $this->assertEquals(
            array('cm' => $commit),
            $this->query->getCommands()
        );
    }

    public function testRemoveWithObjectInput()
    {
        $rollback = new Rollback;
        $this->query->add('rb', $rollback);

        $commit = new Commit;
        $this->query->add('cm', $commit);

        $this->query->remove($rollback);

        $this->assertEquals(
            array('cm' => $commit),
            $this->query->getCommands()
        );
    }

    public function testRemoveInvalidKey()
    {
        $rollback = new Rollback;
        $this->query->add('rb', $rollback);

        $commit = new Commit;
        $this->query->add('cm', $commit);

        $this->query->remove('invalidkey'); //should silently ignore

        $this->assertEquals(
            array('rb' => $rollback, 'cm' => $commit),
            $this->query->getCommands()
        );
    }

    public function testAddRollback()
    {
        $this->query->addRollback();
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_ROLLBACK,
            $commands[0]->getType()
        );
    }

    public function testAddDeleteQuery()
    {
        $this->query->addDeleteQuery('*:*');
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array('*:*'),
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteQueryWithBind()
    {
        $this->query->addDeleteQuery('id:%1%', array(678));
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array('id:678'),
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteQueries()
    {
        $this->query->addDeleteQueries(array('id:1', 'id:2'));
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array('id:1', 'id:2'),
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteById()
    {
        $this->query->addDeleteById(1);
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array(1),
            $commands[0]->getIds()
        );
    }

    public function testAddDeleteByIds()
    {
        $this->query->addDeleteByIds(array(1, 2));
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array(1, 2),
            $commands[0]->getIds()
        );
    }

    public function testAddDocument()
    {
        $doc = new Document(array('id' => 1));

        $this->query->addDocument($doc);
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_ADD,
            $commands[0]->getType()
        );

        $this->assertEquals(
            array($doc),
            $commands[0]->getDocuments()
        );
    }

    public function testAddDocuments()
    {
        $doc1 = new Document(array('id' => 1));
        $doc2 = new Document(array('id' => 1));

        $this->query->addDocuments(array($doc1, $doc2), true, 100);
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_ADD,
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
        $this->query->addCommit(true, false, true);
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_COMMIT,
            $commands[0]->getType()
        );

        $this->assertEquals(
            true,
            $commands[0]->getSoftCommit()
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
        $this->query->addOptimize(true, false, 10);
        $commands = $this->query->getCommands();

        $this->assertEquals(
            Query::COMMAND_OPTIMIZE,
            Query::COMMAND_OPTIMIZE,
            $commands[0]->getType()
        );

        $this->assertEquals(
            true,
            $commands[0]->getSoftCommit()
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
        $type = Query::COMMAND_ROLLBACK;
        $options = array('optionA' => 1, 'optionB' => 2);
        $command = $this->query->createCommand($type, $options);

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
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->query->createCommand('invalidtype');
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertEquals('MyDocument', $this->query->getDocumentClass());
    }

    public function testCreateDocument()
    {
        $doc = $this->query->createDocument();
        $this->assertThat($doc, $this->isInstanceOf($this->query->getDocumentClass()));
    }

    public function testCreateDocumentWithCustomClass()
    {
        $this->query->setDocumentClass(__NAMESPACE__.'\\MyCustomDoc');

        $doc = $this->query->createDocument();
        $this->assertThat($doc, $this->isInstanceOf(__NAMESPACE__.'\\MyCustomDoc'));
    }

    public function testCreateDocumentWithFieldsAndBoostsAndModifiers()
    {
        $fields = array('id' => 1, 'name' => 'testname');
        $boosts = array('name' => 2.7);
        $modifiers = array('name' => 'set');

        $doc = $this->query->createDocument($fields, $boosts, $modifiers);
        $doc->setKey('id');

        $this->assertThat($doc, $this->isInstanceOf($this->query->getDocumentClass()));

        $this->assertEquals(
            $fields,
            $doc->getFields()
        );

        $this->assertEquals(
            2.7,
            $doc->getFieldBoost('name')
        );

        $this->assertEquals(
            $modifiers['name'],
            $doc->getFieldModifier('name')
        );
    }
}

class MyCustomDoc extends Document
{
}
