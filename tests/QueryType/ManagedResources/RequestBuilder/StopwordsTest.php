<?php

namespace Solarium\Tests\QueryType\ManagedResources\Resources\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add as AddCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete as DeleteCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists as ExistsCommand;
use Solarium\QueryType\ManagedResources\RequestBuilder\Stopwords as StopwordsRequestBuilder;

class StopwordsTest extends TestCase
{
    /**
     * @var StopwordsQuery
     */
    protected $query;

    /**
     * @var StopwordsRequestBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new StopwordsQuery();
        $this->builder = new StopwordsRequestBuilder();
    }

    public function testBuild()
    {
        $handler = 'schema/analysis/stopwords/dutch';

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
        $request = $this->builder->build($this->query);
    }

    public function testQuery()
    {
        $this->query->setName('dutch');
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
    }

    public function testAdd()
    {
        $stopwords = ['de'];
        $command = new AddCommand();
        $command->setStopwords($stopwords);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals($stopwords, $command->getStopwords());
        $this->assertEquals('', $command->getTerm());
        $this->assertEquals('["de"]', $command->getRawData());
    }

    public function testDelete()
    {
        $term = 'de';
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
        $term = 'de';
        $command = new ExistsCommand();
        $command->setTerm('de');
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertEquals($term, $command->getTerm());
        $this->assertEquals('', $command->getRawData());
    }
}
