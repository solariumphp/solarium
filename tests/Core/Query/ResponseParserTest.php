<?php

namespace Solarium\Tests\Core\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\AbstractResponseParser;

class ResponseParserTest extends TestCase
{
    /**
     * @var TestResponseParser
     */
    protected $parser;

    public function setup()
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

/**
 * Dummy implementation to test code in abstract class.
 */
class TestResponseParser extends AbstractResponseParser
{
    public function parse($result)
    {
    }
}
