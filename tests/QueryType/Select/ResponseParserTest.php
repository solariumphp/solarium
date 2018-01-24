<?php

namespace Solarium\Tests\QueryType\Select;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\FacetSet;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\ResponseParser;
use Solarium\QueryType\Select\Result\Result;
use Solarium\QueryType\Update\Query\Document\Document;

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
                'maxScore' => 1.23,
            ),
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            )
        );

        $query = new Query(array('documentclass' => Document::class));
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertSame(1, $result['status']);
        $this->assertSame(13, $result['queryTime']);
        $this->assertSame(503, $result['numfound']);
        $this->assertSame(1.23, $result['maxscore']);

        $docs = array(
            new Document(array('fieldA' => 1, 'fieldB' => 'Test')),
            new Document(array('fieldA' => 2, 'fieldB' => 'Test2'))
        );
        $this->assertEquals($docs, $result['documents']);

        $components = array(
            Query::COMPONENT_FACETSET => new FacetSet(array())
        );
        $this->assertEquals($components, $result['components']);
    }

    public function testParseWithoutScore()
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
            )
        );

        $query = new Query(array('documentclass' => Document::class));
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertSame(1, $result['status']);
        $this->assertSame(13, $result['queryTime']);
        $this->assertSame(503, $result['numfound']);
        $this->assertSame(null, $result['maxscore']);

        $docs = array(
            new Document(array('fieldA' => 1, 'fieldB' => 'Test')),
            new Document(array('fieldA' => 2, 'fieldB' => 'Test2'))
        );
        $this->assertEquals($docs, $result['documents']);

        $components = array(
            Query::COMPONENT_FACETSET => new FacetSet(array())
        );
        $this->assertEquals($components, $result['components']);
    }

    public function testParseWithInvalidDocumentClass()
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
            )
        );

        $query = new Query(array('documentclass' => 'StdClass'));
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser();

        $this->expectException(RuntimeException::class);
        $parser->parse($resultStub);
    }

    public function testParseWithoutNumFound()
    {
        $data = array(
            'response' => array(
                'docs' => array(
                    array('fieldA' => 1, 'fieldB' => 'Test'),
                    array('fieldA' => 2, 'fieldB' => 'Test2')
                ),
            ),
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 13,
            )
        );

        $query = new Query(array('documentclass' => Document::class));
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->will($this->returnValue($query));

        $parser = new ResponseParser;
        $result = $parser->parse($resultStub);

        $this->assertSame(1, $result['status']);
        $this->assertSame(13, $result['queryTime']);
        $this->assertNull(null, $result['numfound']);
    }
}
