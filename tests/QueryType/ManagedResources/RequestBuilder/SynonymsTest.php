<?php

namespace Solarium\Tests\QueryType\ManagedResources\Resources\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add as AddCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Config as ConfigCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Create as CreateCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Delete as DeleteCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Exists as ExistsCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Remove as RemoveCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\InitArgs;
use Solarium\QueryType\ManagedResources\RequestBuilder\Synonyms as SynonymsRequestBuilder;
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
        $this->expectException(\Solarium\Exception\RuntimeException::class);
        $this->builder->build($this->query);
    }

    public function testUnsupportedCommand()
    {
        $command = new UnsupportedSynonymsCommand();
        $this->query->setName('dutch');
        $this->query->setCommand($command);

        $this->expectException(\RuntimeException::class);
        $request = $this->builder->build($this->query);
    }

    public function testAdd()
    {
        $synonyms = new SynonymsQuery\Synonyms();
        $command = new AddCommand();

        $command->setSynonyms($synonyms);
        $this->assertEquals('', $command->getRawData());

        $synonyms->setTerm('mad');
        $synonyms->setSynonyms(['angry', 'upset']);
        $command->setSynonyms($synonyms);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals('', $command->getTerm());
        $this->assertEquals('{"mad":["angry","upset"]}', $request->getRawData());
    }

    public function testAddSymmetrical()
    {
        $synonyms = new SynonymsQuery\Synonyms();
        $synonyms->setSynonyms(['funny', 'entertaining', 'whimsical', 'jocular']);
        $command = new AddCommand();
        $command->setSynonyms($synonyms);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals('["funny","entertaining","whimsical","jocular"]', $request->getRawData());
    }

    public function testConfig()
    {
        $initArgs = new InitArgs();
        $command = new ConfigCommand();

        $command->setInitArgs($initArgs);
        $this->assertEquals('', $command->getRawData());

        $initArgs->setInitArgs(['ignoreCase' => true, 'format' => $initArgs::FORMAT_SOLR]);
        $command->setInitArgs($initArgs);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals('', $command->getTerm());
        $this->assertEquals('{"initArgs":{"ignoreCase":true,"format":"solr"}}', $command->getRawData());
    }

    public function testCreate()
    {
        $command = new CreateCommand();
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals('', $command->getTerm());
        $this->assertEquals('{"class":"org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager"}', $command->getRawData());
    }

    public function testDelete()
    {
        $term = 'mad';
        $command = new DeleteCommand();
        $command->setTerm($term);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertEquals($term, $command->getTerm());
        $this->assertEquals('', $command->getRawData());
    }

    public function testRemove()
    {
        $command = new RemoveCommand();
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertEquals('', $command->getTerm());
        $this->assertEquals('', $command->getRawData());
    }

    public function testExists()
    {
        $term = 'mad';
        $command = new ExistsCommand();
        $command->setTerm($term);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertEquals($term, $command->getTerm());
        $this->assertEquals('', $command->getRawData());
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
