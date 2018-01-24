<?php

namespace Solarium\Tests\QueryType\Suggester;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Suggester\Query;
use Solarium\QueryType\Suggester\ResponseParser;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Result;
use Solarium\QueryType\Suggester\Result\Term;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = array(
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            ),
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
        );

        $query = new Query();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->any())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $expected = array(
            'dictionary1' => new Dictionary([
                'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
                'zoo' => new Term(1, [['term' => 'zoo keeper']]),
            ]),
            'dictionary2' => new Dictionary([
                'free' => new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
            ]),
        );
        $allExpected = array(
            new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            new Term(1, [['term' => 'zoo keeper']]),
            new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        );

        $this->assertEquals($expected, $result['results']);
        $this->assertEquals($allExpected, $result['all']);
    }
}
