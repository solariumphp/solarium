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
use Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Add as AddCommand;
use Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Create as CreateCommand;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\Query\Synonyms\InitArgs;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as SynonymsRequestBuilder;

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

    public function setUp(): void
    {
        $this->query = new SynonymsQuery();
        $this->query->setName('dutch');
        $this->builder = new SynonymsRequestBuilder();
    }

    public function testBuild()
    {
        $handler = 'schema/analysis/synonyms/dutch';
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
        $this->builder->build($this->query);
    }

    public function testUnsupportedCommand()
    {
        $command = new UnsupportedSynonymsCommand();

        $this->query->setCommand($command);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unsupported command type: unsupportedtype');
        $request = $this->builder->build($this->query);
    }

    public function testQuery()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testQueryWithTerm()
    {
        $this->query->setTerm('mad');
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch/mad', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testAdd()
    {
        $synonyms = new SynonymsQuery\Synonyms();
        $synonyms->setTerm('mad');
        $synonyms->setSynonyms(['angry', 'upset']);

        $command = new AddCommand();
        $command->setSynonyms($synonyms);

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_JSON, $request->getContentType());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('{"mad":["angry","upset"]}', $request->getRawData());
    }

    public function testAddSymmetrical()
    {
        $synonyms = new SynonymsQuery\Synonyms();
        $synonyms->setSynonyms(['funny', 'entertaining', 'whimsical', 'jocular']);

        $command = new AddCommand();
        $command->setSynonyms($synonyms);

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_JSON, $request->getContentType());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('["funny","entertaining","whimsical","jocular"]', $request->getRawData());
    }

    public function testAddWithoutSynonyms()
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
        $initArgs->setInitArgs(['ignoreCase' => true, 'format' => $initArgs::FORMAT_SOLR]);

        $command = new ConfigCommand();
        $command->setInitArgs($initArgs);

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_PUT, $request->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_JSON, $request->getContentType());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('{"initArgs":{"ignoreCase":true,"format":"solr"}}', $request->getRawData());
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
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_JSON, $request->getContentType());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertSame('{"class":"org.apache.solr.rest.schema.analysis.ManagedSynonymGraphFilterFactory$SynonymManager"}', $request->getRawData());
    }

    public function testCreateWithoutClass()
    {
        $command = new UnsupportedSynonymsCreateCommand();

        $this->query->setCommand($command);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing class for CREATE command.');
        $request = $this->builder->build($this->query);
    }

    public function testDelete()
    {
        $command = new DeleteCommand();
        $command->setTerm('mad');

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch/mad', $request->getHandler());
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
        $command->setTerm('mad');

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        // SOLR-15116 and SOLR-16274 force us to use GET by default
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch/mad', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testExistsWithoutTerm()
    {
        $command = new ExistsCommand();

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        // SOLR-16274 forces us to use GET by default
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testExistsWithUseHeadRequest()
    {
        $command = new ExistsCommand();
        $command->setUseHeadRequest(true);

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_HEAD, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    public function testRemove()
    {
        $command = new RemoveCommand();

        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_DELETE, $request->getMethod());
        $this->assertSame('schema/analysis/synonyms/dutch', $request->getHandler());
        $this->assertNull($request->getRawData());
    }

    /**
     * Reserved characters per RFC 3986 in a REST resource name need to be percent-encoded;
     * + the percent character that serves as the indicator for percent-encoded octets;
     * + the space character that mustn't be confused with embedded whitespace.
     *
     * When talking with Solr, these characters actually need to be encoded twice to make it
     * through the servlet, effectively encoding every octet indicator again (SOLR-6853).
     *
     * @see https://datatracker.ietf.org/doc/html/rfc3986#section-2
     * @see https://issues.apache.org/jira/browse/SOLR-6853
     */
    public function testReservedCharacters()
    {
        $unencoded = ':/?#[]@% ';
        $encoded = '%253A%252F%253F%2523%255B%255D%2540%2525%2520';
        $command = new ExistsCommand();
        $command->setTerm($unencoded);
        $this->query->setName($unencoded);
        $this->query->setCommand($command);
        $request = $this->builder->build($this->query);
        $this->assertStringEndsWith('/'.$encoded.'/'.$encoded, $request->getHandler());
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

class UnsupportedSynonymsCreateCommand extends AbstractCommand
{
    public function getType(): string
    {
        return SynonymsQuery::COMMAND_CREATE;
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
