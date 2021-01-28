<?php

namespace Solarium\Tests\QueryType\ManagedResources\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Config as ConfigCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Exists as ExistsCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Remove as RemoveCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Add as AddCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Create as CreateCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms\InitArgs;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as SynonymsRequestBuilder;
use Solarium\Tests\Integration\TestClientFactory;

class SynonymsTest extends TestCase
{
    /**
     * @var SynonymsQuery
     */
    protected $query;

    /**
     * @var SynonymsRequestBuilder
     */
    protected $builder;

    /**
     * @var Client
     */
    protected $client;

    public function setUp(): void
    {
        $this->query = new SynonymsQuery();
        $this->builder = new SynonymsRequestBuilder();
        $this->client = TestClientFactory::createWithCurlAdapter();
    }

    public function testBuild()
    {
        $handler = 'schema/analysis/synonyms/dutch';
        $this->query->setName('dutch');
        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
                'omitHeader' => 'true',
            ],
            $request->getParams()
        );

        $this->assertSame($handler, $request->getHandler());
    }

    public function testNoName()
    {
        $this->expectException(RuntimeException::class);
        $this->builder->build($this->query);
    }

    public function testUnsupportedCommand()
    {
        $command = new UnsupportedSynonymsCommand();
        $this->query->setName('dutch');
        $this->query->setCommand($command);

        $this->expectException(RuntimeException::class);
        $request = $this->builder->build($this->query);
    }

    public function testQuery()
    {
        $this->query->setName('dutch');
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testAdd()
    {
        $synonyms = new SynonymsQuery\Synonyms();
        $command = new AddCommand();
        $command->setSynonyms($synonyms);
        $this->assertSame($synonyms, $command->getSynonyms());
        $this->assertSame('', $command->getRawData());

        $synonyms->setTerm('mad');
        $synonyms->setSynonyms(['angry', 'upset']);
        $command->setSynonyms($synonyms);
        $this->assertSame($synonyms, $command->getSynonyms());
        $this->assertSame('{"mad":["angry","upset"]}', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('{"mad":["angry","upset"]}', $request->getRawData());
    }

    public function testAddSymmetrical()
    {
        $synonyms = new SynonymsQuery\Synonyms();
        $synonyms->setSynonyms(['funny', 'entertaining', 'whimsical', 'jocular']);
        $command = new AddCommand();
        $command->setSynonyms($synonyms);
        $this->assertSame($synonyms, $command->getSynonyms());
        $this->assertSame('["funny","entertaining","whimsical","jocular"]', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('["funny","entertaining","whimsical","jocular"]', $request->getRawData());
    }

    public function testConfig()
    {
        $initArgs = new InitArgs();
        $command = new ConfigCommand();

        $command->setInitArgs($initArgs);
        $this->assertSame($initArgs, $command->getInitArgs());
        $this->assertSame('', $command->getRawData());

        $initArgs->setInitArgs(['ignoreCase' => true, 'format' => $initArgs::FORMAT_SOLR]);
        $command->setInitArgs($initArgs);
        $this->assertSame($initArgs, $command->getInitArgs());
        $this->assertSame('{"initArgs":{"ignoreCase":true,"format":"solr"}}', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('{"initArgs":{"ignoreCase":true,"format":"solr"}}', $request->getRawData());
    }

    public function testCreate()
    {
        $command = new CreateCommand();
        $this->assertSame('{"class":"org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager"}', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('{"class":"org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager"}', $request->getRawData());
    }

    public function testDelete()
    {
        $command = new DeleteCommand();
        $command->setTerm('mad');
        $this->assertSame('mad', $command->getTerm());
        $this->assertSame('', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch/mad', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testExists()
    {
        $command = new ExistsCommand();
        $command->setTerm('mad');
        $this->assertSame('mad', $command->getTerm());
        $this->assertSame('', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch/mad', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testExistsWithoutTerm()
    {
        $command = new ExistsCommand();
        $this->assertNull($command->getTerm());
        $this->assertSame('', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testRemove()
    {
        $command = new RemoveCommand();
        $this->assertEquals('', $command->getRawData());

        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }
}

class UnsupportedSynonymsCommand extends AbstractCommand
{
    public function getType(): string
    {
        return 'unsupportedtype';
    }

    public function getRequestMethod(): string
    {
        return '';
    }

    public function getRawData(): string
    {
        return '';
    }

    public function getTerm(): string
    {
        return '';
    }
}
