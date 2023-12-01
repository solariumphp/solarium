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

    public function testConvertToKeyValueArray()
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

    public function testConvertToKeyValueArrayWithRepeatingKey()
    {
        $input = [
            'key1',
            'value1',
            'key2',
            'value2',
            'key2',
            'value3',
        ];

        $expected = [
            'key1' => 'value1',
            'key2' => ['value2', 'value3'],
        ];

        $this->assertSame(
            $expected,
            $this->parser->convertToKeyValueArray($input)
        );
    }

    public function testConvertToValueArray()
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
            'value1',
            'value2',
            'value3',
        ];

        $this->assertSame(
            $expected,
            $this->parser->convertToValueArray($input)
        );
    }

    public function testConvertToValueArrayWithRepeatingKey()
    {
        $input = [
            'key1',
            'value1',
            'key2',
            'value2',
            'key2',
            'value3',
        ];

        $expected = [
            'value1',
            'value2',
            'value3',
        ];

        $this->assertSame(
            $expected,
            $this->parser->convertToValueArray($input)
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
