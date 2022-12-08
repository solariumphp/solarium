<?php

namespace Solarium\Tests\QueryType\Update\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Command\Rollback;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;

class QueryTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_UPDATE, $this->query->getType());
    }

    public function testDefaultRequestFormat()
    {
        $this->assertSame(
            Query::REQUEST_FORMAT_JSON,
            $this->query->getRequestFormat(),
            // some tests will still pass but no longer be reliable if they're suddenly testing against the default
            'Update all tests that assume REQUEST_FORMAT_JSON is the default if this is changed (including tests for plugins)'
        );
    }

    public function testSetAndGetRequestFormat()
    {
        $this->query->setRequestFormat(Query::REQUEST_FORMAT_XML);
        $this->assertSame(Query::REQUEST_FORMAT_XML, $this->query->getRequestFormat());
    }

    public function testSetUnsupportedRequestFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported request format: foobar');
        $this->query->setRequestFormat('foobar');
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Update\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetJsonRequestBuilder()
    {
        $this->query->setRequestFormat(Query::REQUEST_FORMAT_JSON);
        $this->assertInstanceOf('Solarium\QueryType\Update\RequestBuilder\Json', $this->query->getRequestBuilder());
    }

    public function testGetXmlRequestBuilder()
    {
        $this->query->setRequestFormat(Query::REQUEST_FORMAT_XML);
        $this->assertInstanceOf('Solarium\QueryType\Update\RequestBuilder\Xml', $this->query->getRequestBuilder());
    }

    public function testConfigMode()
    {
        $options = [
            'handler' => 'myHandler',
            'requestformat' => Query::REQUEST_FORMAT_XML,
            'resultclass' => 'myResult',
            'command' => [
                'key1' => [
                    'type' => 'delete',
                    'query' => 'population:[* TO 1000]',
                    'id' => [1, 2],
                ],
                'key2' => [
                    'type' => 'commit',
                    'softcommit' => true,
                    'waitsearcher' => false,
                    'expungedeletes' => true,
                ],
                'key3' => [
                    'type' => 'optimize',
                    'softcommit' => true,
                    'waitsearcher' => false,
                    'maxsegments' => 5,
                ],
                'key4' => [
                    'type' => 'rollback',
                ],
            ],
        ];
        $this->query->setOptions($options);
        $commands = $this->query->getCommands();

        $this->assertSame(
            $options['handler'],
            $this->query->getHandler()
        );

        $this->assertSame(
            Query::REQUEST_FORMAT_XML,
            $this->query->getRequestFormat()
        );

        $this->assertSame(
            $options['resultclass'],
            $this->query->getResultClass()
        );

        $delete = $commands['key1'];
        $this->assertSame(
            [1, 2],
            $delete->getIds()
        );
        $this->assertSame(
            ['population:[* TO 1000]'],
            $delete->getQueries()
        );

        $commit = $commands['key2'];
        $this->assertTrue(
            $commit->getSoftCommit()
        );
        $this->assertFalse(
            $commit->getWaitSearcher()
        );
        $this->assertTrue(
            $commit->getExpungeDeletes()
        );

        $optimize = $commands['key3'];
        $this->assertTrue(
            $optimize->getSoftCommit()
        );
        $this->assertFalse(
            $optimize->getWaitSearcher()
        );
        $this->assertSame(
            5,
            $optimize->getMaxSegments()
        );

        $rollback = $commands['key4'];
        $this->assertSame(
            Rollback::class,
            get_class($rollback)
        );
    }

    public function testConstructorWithUnsupportedRequestFormat()
    {
        $config = [
            'requestformat' => 'foobar',
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported request format: foobar');
        new Query($config);
    }

    public function testConstructorWithConfigAddCommand()
    {
        $config = [
            'command' => [
                'key1' => [
                    'type' => 'add',
                ],
            ],
        ];

        $this->expectException(RuntimeException::class);
        new Query($config);
    }

    public function testAddWithoutKey()
    {
        $command = new Rollback();
        $this->query->add(null, $command);

        $this->assertSame(
            [$command],
            $this->query->getCommands()
        );
    }

    public function testAddWithKey()
    {
        $rollback = new Rollback();
        $this->query->add('rb', $rollback);

        $commit = new Commit();
        $this->query->add('cm', $commit);

        $this->assertSame(
            ['rb' => $rollback, 'cm' => $commit],
            $this->query->getCommands()
        );
    }

    public function testRemove()
    {
        $rollback = new Rollback();
        $this->query->add('rb', $rollback);

        $commit = new Commit();
        $this->query->add('cm', $commit);

        $this->query->remove('rb');

        $this->assertSame(
            ['cm' => $commit],
            $this->query->getCommands()
        );
    }

    public function testRemoveWithObjectInput()
    {
        $rollback = new Rollback();
        $this->query->add('rb', $rollback);

        $commit = new Commit();
        $this->query->add('cm', $commit);

        $this->query->remove($rollback);

        $this->assertSame(
            ['cm' => $commit],
            $this->query->getCommands()
        );
    }

    public function testRemoveInvalidKey()
    {
        $rollback = new Rollback();
        $this->query->add('rb', $rollback);

        $commit = new Commit();
        $this->query->add('cm', $commit);

        $this->query->remove('invalidkey'); // should silently ignore

        $this->assertSame(
            ['rb' => $rollback, 'cm' => $commit],
            $this->query->getCommands()
        );
    }

    public function testAddRollback()
    {
        $this->query->addRollback();
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_ROLLBACK,
            $commands[0]->getType()
        );
    }

    public function testAddDeleteQuery()
    {
        $this->query->addDeleteQuery('*:*');
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertSame(
            ['*:*'],
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteQueryWithBind()
    {
        $this->query->addDeleteQuery('id:%1%', [678]);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertSame(
            ['id:678'],
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteQueries()
    {
        $this->query->addDeleteQueries(['id:1', 'id:2']);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertSame(
            ['id:1', 'id:2'],
            $commands[0]->getQueries()
        );
    }

    public function testAddDeleteById()
    {
        $this->query->addDeleteById(1);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertSame(
            [1],
            $commands[0]->getIds()
        );
    }

    public function testAddDeleteByIds()
    {
        $this->query->addDeleteByIds([1, 2]);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_DELETE,
            $commands[0]->getType()
        );

        $this->assertSame(
            [1, 2],
            $commands[0]->getIds()
        );
    }

    public function testAddDocument()
    {
        $doc = new Document(['id' => 1]);

        $this->query->addDocument($doc);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_ADD,
            $commands[0]->getType()
        );

        $this->assertSame(
            [$doc],
            $commands[0]->getDocuments()
        );
    }

    public function testAddDocuments()
    {
        $doc1 = new Document(['id' => 1]);
        $doc2 = new Document(['id' => 1]);

        $this->query->addDocuments([$doc1, $doc2], true, 100);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_ADD,
            $commands[0]->getType()
        );

        $this->assertSame(
            [$doc1, $doc2],
            $commands[0]->getDocuments()
        );

        $this->assertTrue(
            $commands[0]->getOverwrite()
        );

        $this->assertSame(
            100,
            $commands[0]->getCommitWithin()
        );
    }

    public function testAddCommit()
    {
        $this->query->addCommit(true, false, true);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_COMMIT,
            $commands[0]->getType()
        );

        $this->assertTrue(
            $commands[0]->getSoftCommit()
        );

        $this->assertFalse(
            $commands[0]->getWaitSearcher()
        );

        $this->assertTrue(
            $commands[0]->getExpungeDeletes()
        );
    }

    public function testAddOptimize()
    {
        $this->query->addOptimize(true, false, 10);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_OPTIMIZE,
            $commands[0]->getType()
        );

        $this->assertTrue(
            $commands[0]->getSoftCommit()
        );

        $this->assertFalse(
            $commands[0]->getWaitSearcher()
        );

        $this->assertSame(
            10,
            $commands[0]->getMaxSegments()
        );
    }

    public function testAddRawXmlCommand()
    {
        $this->query->addRawXmlCommand('<add><doc><field name="id">1</field></doc></add>');
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_RAWXML,
            $commands[0]->getType()
        );

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $commands[0]->getCommands()
        );
    }

    public function testAddRawXmlCommands()
    {
        $this->query->addRawXmlCommands(['<add><doc><field name="id">1</field></doc></add>', '<add><doc><field name="id">2</field></doc></add>']);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_RAWXML,
            $commands[0]->getType()
        );

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>', '<add><doc><field name="id">2</field></doc></add>'],
            $commands[0]->getCommands()
        );
    }

    public function testAddRawXmlFile()
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'xml');
        file_put_contents($tmpfname, '<add><doc><field name="id">1</field></doc></add>');

        $this->query->addRawXmlFile($tmpfname);
        $commands = $this->query->getCommands();

        $this->assertSame(
            Query::COMMAND_RAWXML,
            $commands[0]->getType()
        );

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $commands[0]->getCommands()
        );

        unlink($tmpfname);
    }

    public function testCreateCommand()
    {
        $type = Query::COMMAND_ROLLBACK;
        $options = ['optionA' => 1, 'optionB' => 2];
        $command = $this->query->createCommand($type, $options);

        // check command type
        $this->assertSame(
            $type,
            $command->getType()
        );

        // check option forwarding
        $commandOptions = $command->getOptions();
        $this->assertSame(
            $options['optionB'],
            $commandOptions['optionB']
        );
    }

    public function testCreateCommandWithInvalidQueryType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->query->createCommand('invalidtype');
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertSame('MyDocument', $this->query->getDocumentClass());
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
        $fields = ['id' => 1, 'name' => 'testname'];
        $boosts = ['name' => 2.7];
        $modifiers = ['name' => 'set'];

        $doc = $this->query->createDocument($fields, $boosts, $modifiers);
        $doc->setKey('id');

        $this->assertThat($doc, $this->isInstanceOf($this->query->getDocumentClass()));

        $this->assertSame(
            $fields,
            $doc->getFields()
        );

        $this->assertSame(
            2.7,
            $doc->getFieldBoost('name')
        );

        $this->assertSame(
            $modifiers['name'],
            $doc->getFieldModifier('name')
        );
    }
}

class MyCustomDoc extends Document
{
}
