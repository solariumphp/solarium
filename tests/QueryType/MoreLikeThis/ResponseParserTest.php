<?php


namespace Solarium\Tests\QueryType\MoreLikeThis;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\MoreLikeThis\Query;
use Solarium\QueryType\MoreLikeThis\ResponseParser;
use Solarium\QueryType\MoreLikeThis\Result;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = array(
            'response' => array(
                'docs' => array(
                    array('fieldA' => 1, 'fieldB' => 'Test'),
                    array('fieldA' => 2, 'fieldB' => 'Test2')
                ),
                'numFound' => 503,
            ),
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            ),
            'interestingTerms' => array(
                'key1', 'value1', 'key2', 'value2'
            ),
            'match' => array(
                'docs' => array(
                    array('fieldA' => 5, 'fieldB' => 'Test5'),
                ),
            ),
        );

        $query = new Query();
        $query->setInterestingTerms('details');
        $query->setMatchInclude(true);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->any())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser;
        $result = $parser->parse($resultStub);

        $this->assertSame(array('key1' => 'value1', 'key2' => 'value2'), $result['interestingTerms']);
        $this->assertSame(5, $result['match']->fieldA);
    }
}
