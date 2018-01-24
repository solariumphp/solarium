<?php

namespace Solarium\Tests\QueryType\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Spellcheck\Query;
use Solarium\QueryType\Spellcheck\ResponseParser;
use Solarium\QueryType\Spellcheck\Result\Result;
use Solarium\QueryType\Spellcheck\Result\Term;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = array(
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            ),
            'spellcheck' => array(
                'suggestions' => array(
                    'd',
                    array(
                        'numFound' => 2,
                        'startOffset' => 3,
                        'endOffset' => 7,
                        'suggestion' => array(
                            'disk',
                            'ddr',
                        ),
                    ),
                    'vid',
                    array(
                        'numFound' => 1,
                        'startOffset' => 2,
                        'endOffset' => 5,
                        'suggestion' => array(
                            'video',
                        ),
                    ),
                    'vid',
                    array(
                        'numFound' => 1,
                        'startOffset' => 6,
                        'endOffset' => 9,
                        'suggestion' => array(
                            'video',
                        ),
                    ),
                    'collation',
                    'disk video',
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
            'd' => new Term(2, 3, 7, array('disk', 'ddr')),
            'vid' => new Term(1, 2, 5, array('video')),
        );
        $allExpected = array(
            new Term(2, 3, 7, array('disk', 'ddr')),
            new Term(1, 2, 5, array('video')),
            new Term(1, 6, 9, array('video')),
        );

        $this->assertEquals($expected, $result['results']);
        $this->assertEquals($allExpected, $result['all']);
        $this->assertSame('disk video', $result['collation']);
    }
}
