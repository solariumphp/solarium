<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Highlighting\Highlighting;
use Solarium\Component\ResponseParser\Highlighting as Parser;
use Solarium\Component\Result\Highlighting\Result;
use Solarium\QueryType\Select\Query\Query;

class HighlightingTest extends TestCase
{
    protected Parser $parser;

    protected Query $query;

    protected Highlighting $highlighting;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->highlighting = $this->query->getHighlighting();
    }

    public function testParse(): void
    {
        $highlights = ['key1' => ['dummy1'], 'key2' => ['dummy2']];
        $data = ['highlighting' => $highlights];
        $expected = [
            'key1' => new Result(['dummy1']),
            'key2' => new Result(['dummy2']),
        ];

        $result = $this->parser->parse($this->query, $this->highlighting, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseNoData(): void
    {
        $result = $this->parser->parse($this->query, $this->highlighting, []);

        $this->assertEquals([], $result->getResults());
    }
}
