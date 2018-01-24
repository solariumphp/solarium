<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\MoreLikeThis as Parser;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Document;

class MoreLikeThisTest extends TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function testParse()
    {
        $query = new Query();
        $data = array(
            'moreLikeThis' => array(
                'id1' => array(
                    'numFound' => 12,
                    'maxScore' => 1.75,
                    'docs' => array(
                        array('field1' => 'value1'),
                    ),
                ),
            ),
        );

        $docs = array(new Document(array('field1' => 'value1')));
        $expected = array(
            'id1' => new Result(12, 1.75, $docs),
        );

        $result = $this->parser->parse($query, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse(null, null, array());

        $this->assertEquals(array(), $result->getResults());
    }

    public function testParseWithoutMaxScore()
    {
        $query = new Query();
        $data = array(
            'moreLikeThis' => array(
                'id1' => array(
                    'numFound' => 12,
                    'docs' => array(
                        array('field1' => 'value1'),
                    ),
                ),
            ),
        );

        $docs = array(new Document(array('field1' => 'value1')));
        $expected = array(
            'id1' => new Result(12, null, $docs),
        );

        $result = $this->parser->parse($query, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }
}
