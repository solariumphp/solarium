<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Highlighting as Parser;
use Solarium\Component\Result\Highlighting\Result;

class HighlightingTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    public function setUp(): void
    {
        $this->parser = new Parser();
    }

    public function testParse()
    {
        $highlights = ['key1' => ['dummy1'], 'key2' => ['dummy2']];
        $data = ['highlighting' => $highlights];
        $expected = [
            'key1' => new Result(['dummy1']),
            'key2' => new Result(['dummy2']),
        ];

        $result = $this->parser->parse(null, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse(null, null, []);

        $this->assertEquals([], $result->getResults());
    }
}
