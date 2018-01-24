<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Suggester;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

class SuggesterTest extends TestCase
{
    protected $parser;
    protected $query;

    public function setUp()
    {
        $this->query = new Query();
        $this->parser = new Suggester();
    }

    /**
     * @dataProvider providerParse
     * @param mixed $data
     */
    public function testParse($data)
    {
        $result = $this->parser->parse($this->query, null, $data);

        $expected = new Dictionary([
            'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            'zoo' => new Term(1, [['term' => 'zoo keeper']]),
        ]);

        $this->assertEquals($expected, $result->getDictionary('dictionary1'));

        $expected = new Dictionary([
            'free' => new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        ]);

        $this->assertEquals($expected, $result->getDictionary('dictionary2'));

        $allExpected = array(
            new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            new Term(1, [['term' => 'zoo keeper']]),
            new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        );

        $this->assertEquals($allExpected, $result->getAll());
    }

    public function providerParse()
    {
        return array(
            0 => array(
                'data' => array(
                    'suggest' => array(
                        'dictionary1' => array(
                            'foo' => array(
                                'numFound' => 2,
                                'suggestions' => array(
                                    array(
                                        'term' => 'foo',
                                    ),
                                    array(
                                        'term' => 'foobar',
                                    ),
                                ),
                            ),
                            'zoo' => array(
                                'numFound' => 1,
                                'suggestions' => array(
                                    array(
                                        'term' => 'zoo keeper',
                                    ),
                                ),
                            ),
                        ),
                        'dictionary2' => array(
                            'free' => array(
                                'numFound' => 2,
                                'suggestions' => array(
                                    array(
                                        'term' => 'free beer',
                                    ),
                                    array(
                                        'term' => 'free software',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, null, array());

        $this->assertNull($result);
    }
}
