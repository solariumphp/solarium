<?php

namespace Solarium\Tests\QueryType\ManagedResources\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add as AddCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Config as ConfigCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Create as CreateCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete as DeleteCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists as ExistsCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Remove as RemoveCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs;
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

    public function setUp(): void
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
        $this->expectException(RuntimeException::class);
        $request = $this->builder->build($this->query);
    }

    public function testUnsupportedCommand()
    {
        $command = new UnsupportedStopwordsCommand();
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

    public function testConfig()
    {
        $initArgs = new InitArgs();
        $command = new ConfigCommand();

        $command->setInitArgs($initArgs);
        $this->assertEquals('', $command->getRawData());

        $initArgs->setInitArgs(['ignoreCase' => true]);
        $command->setInitArgs($initArgs);
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals('', $command->getTerm());
        $this->assertEquals('{"initArgs":{"ignoreCase":true}}', $command->getRawData());
    }

    public function testCreate()
    {
        $command = new CreateCommand();
        $this->query->setName('dutch');
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertEquals('', $command->getTerm());
        $this->assertEquals('{"class":"org.apache.solr.rest.schema.analysis.ManagedWordSetResource"}', $command->getRawData());
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
}

class UnsupportedStopwordsCommand extends AbstractCommand
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
