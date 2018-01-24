<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Stats as Parser;

class StatsTest extends TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function testParse()
    {
        $data = array(
            'stats' => array(
                'stats_fields' => array(
                    'fieldA' => array(
                        'min' => 3,
                    ),
                    'fieldB' => array(
                        'min' => 4,
                        'facets' => array(
                            'fieldC' => array(
                                'value1' => array(
                                    'min' => 5,
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $result = $this->parser->parse(null, null, $data);

        $this->assertEquals(3, $result->getResult('fieldA')->getMin());
        $this->assertEquals(4, $result->getResult('fieldB')->getMin());

        $facets = $result->getResult('fieldB')->getFacets();
        $this->assertEquals(5, $facets['fieldC']['value1']->getMin());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse(null, null, array());
        $this->assertCount(0, $result);
    }
}
