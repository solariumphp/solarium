<?php

namespace Solarium\Tests\QueryType\ManagedResources\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\ManagedResources\Query\AbstractCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Config as ConfigCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Exists as ExistsCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Remove as RemoveCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Add as AddCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Create as CreateCommand;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as StopwordsRequestBuilder;

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
        $this->query->setName('dutch');
        $this->builder = new StopwordsRequestBuilder();
    }

    public function testBuild()
    {
        $handler = 'schema/analysis/stopwords/dutch';
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
        $this->query->setName('');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Name of the resource is not set in the query.');
        $request = $this->builder->build($this->query);
    }

    public function testUnsupportedCommand()
    {
        $command = new UnsupportedStopwordsCommand();

        $this->query->setCommand($command);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported command type: unsupportedtype');
        $request = $this->builder->build($this->query);
    }

    public function testQuery()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testQueryWithTerm()
    {
        $this->query->setTerm('de');
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch/de', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testAdd()
    {
        $command = new AddCommand();
        $command->setStopwords(['de']);

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch', $request->getHandler());
        $this->assertSame('["de"]', $request->getRawData());
    }

    public function testAddWithoutStopwords()
    {
        $command = new AddCommand();

        $this->query->setCommand($command);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing data for ADD command.');
        $request = $this->builder->build($this->query);
    }

    public function testConfig()
    {
        $initArgs = new InitArgs();
        $initArgs->setInitArgs(['ignoreCase' => true]);

        $command = new ConfigCommand();
        $command->setInitArgs($initArgs);

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch', $request->getHandler());
        $this->assertSame('{"initArgs":{"ignoreCase":true}}', $request->getRawData());
    }

    public function testConfigWithoutInitArgs()
    {
        $command = new ConfigCommand();

        $this->query->setCommand($command);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing initArgs for CONFIG command.');
        $request = $this->builder->build($this->query);
    }

    public function testCreate()
    {
        $command = new CreateCommand();

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch', $request->getHandler());
        $this->assertSame('{"class":"org.apache.solr.rest.schema.analysis.ManagedWordSetResource"}', $request->getRawData());
    }

    public function testCreateWithoutClass()
    {
        $command = new UnsupportedStopwordsCreateCommand();

        $this->query->setCommand($command);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing class for CREATE command.');
        $request = $this->builder->build($this->query);
    }

    public function testDelete()
    {
        $command = new DeleteCommand();
        $command->setTerm('de');

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch/de', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testDeleteWithoutTerm()
    {
        $command = new DeleteCommand();

        $this->query->setCommand($command);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing term for DELETE command.');
        $request = $this->builder->build($this->query);
    }

    public function testExists()
    {
        $command = new ExistsCommand();
        $command->setTerm('de');

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        // there's a bug since Solr 8.7 with HEAD requests if a term is set (SOLR-15116)
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch/de', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testExistsWithoutTerm()
    {
        $command = new ExistsCommand();

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_HEAD, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testRemove()
    {
        $command = new RemoveCommand();

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertSame('schema/analysis/stopwords/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
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

class UnsupportedStopwordsCreateCommand extends AbstractCommand
{
    public function getType(): string
    {
        return StopwordsQuery::COMMAND_CREATE;
    }

    public function getRequestMethod(): string
    {
        return Request::METHOD_PUT;
    }

    public function getRawData(): ?string
    {
        return null;
    }
}
