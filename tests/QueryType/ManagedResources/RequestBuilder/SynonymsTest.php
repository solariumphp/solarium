<?php

namespace Solarium\Tests\QueryType\ManagedResources\Resources\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add as AddCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Delete as DeleteCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Exists as ExistsCommand;
use Solarium\QueryType\ManagedResources\RequestBuilder\Synonyms as SynonymsRequestBuilder;

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

    public function setUp()
    {
        $this->query = new SynonymsQuery();
        $this->builder = new SynonymsRequestBuilder();
        $this->client = new Client();
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

    public function testAdd()
    {
        $synonyms = new SynonymsQuery\Synonyms();
        $synonyms->setTerm('mad');
        $synonyms->setSynonyms(['angry', 'upset']);
        $command = new AddCommand();
        $command->setSynonyms($synonyms);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals('{"mad":["angry","upset"]}', $request->getRawData());
    }

    public function testAddSymmytrical()
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
