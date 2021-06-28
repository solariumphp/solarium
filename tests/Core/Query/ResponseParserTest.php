<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractResponseParser;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\Result;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\Core\Query\Status4xxNoExceptionInterface;

class ResponseParserTest extends TestCase
{
    /**
     * @var TestResponseParser
     */
    protected $parser;

    public function setUp(): void
    {
        $this->parser = new TestResponseParser();
    }

    public function testBuild()
    {
        $input = [
            'key1',
            'value1',
            'key2',
            'value2',
            'key3',
            'value3',
        ];

        $expected = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $this->assertSame(
            $expected,
            $this->parser->convertToKeyValueArray($input)
        );
    }

    /**
     * @testWith [200, true]
     *           [400, false]
     */
    public function testParseStatus(int $statusCode, bool $expectedSuccess)
    {
        $result = new Result(
            new TestQueryForResponseParser(),
            new Response('', [sprintf('HTTP/1.1 %d Status', $statusCode)])
        );

        $expected = [
            'wasSuccessful' => $expectedSuccess,
            'statusMessage' => 'Status',
        ];

        $this->assertSame(
            $expected,
            $this->parser->parse($result)
        );
    }

    public function testAddHeaderInfo()
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 5,
            ],
        ];
        $result = ['key' => 'value'];
        $expected = [
            'key' => 'value',
            'status' => 0,
            'queryTime' => 5,
        ];

        $this->assertSame($expected, $this->parser->addHeaderInfo($data, $result));
    }

    public function testAddHeaderInfoEmpty()
    {
        $data = [];
        $result = ['key' => 'value'];
        $expected = [
            'key' => 'value',
            'status' => null,
            'queryTime' => null,
        ];

        $this->assertSame($expected, $this->parser->addHeaderInfo($data, $result));
    }
}

class TestQueryForResponseParser extends AbstractQuery implements Status4xxNoExceptionInterface
{
    public function getType(): string
    {
        return 'testType';
    }

    public function getRequestBuilder(): RequestBuilderInterface
    {
        return null;
    }

    public function getResponseParser(): ResponseParserInterface
    {
        return null;
    }
}

/**
 * Dummy implementation to test code in abstract class.
 */
class TestResponseParser extends AbstractResponseParser
{
    public function parse(ResultInterface $result): array
    {
        return $this->parseStatus([], $result);
    }
}
