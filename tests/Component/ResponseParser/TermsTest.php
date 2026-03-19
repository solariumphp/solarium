<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Terms as Parser;
use Solarium\Component\Terms;
use Solarium\QueryType\Select\Query\Query;

class TermsTest extends TestCase
{
    protected Parser $parser;

    protected Query $query;

    protected Terms $terms;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->terms = new Terms();
    }

    public function testParse(): void
    {
        $data = [
            'terms' => [
                'name' => [
                    'one',
                    5,
                    '184',
                    3,
                    '1gb',
                    3,
                    '3200',
                    3,
                    '400',
                    3,
                    'ddr',
                    3,
                    'gb',
                    3,
                    'ipod',
                    3,
                    'memory',
                    3,
                    'pc',
                    3,
                ],
            ],
        ];

        $result = $this->parser->parse($this->query, $this->terms, $data);

        $this->assertEquals([
            'one',
            184,
            '1gb',
            3200,
            400,
            'ddr',
            'gb',
            'ipod',
            'memory',
            'pc',
        ], $result->getField('name')->getTerms());

        $this->assertEquals([
            'one' => 5,
            184 => 3,
            '1gb' => 3,
            3200 => 3,
            400 => 3,
            'ddr' => 3,
            'gb' => 3,
            'ipod' => 3,
            'memory' => 3,
            'pc' => 3,
        ], $result->getAll()['name']);
    }

    public function testParseNoData(): void
    {
        $result = $this->parser->parse($this->query, $this->terms, []);

        $this->assertNull($result);
    }
}
