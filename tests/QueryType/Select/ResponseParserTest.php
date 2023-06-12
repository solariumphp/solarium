<?php

namespace Solarium\Tests\QueryType\Select;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\FacetSet;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\ResponseParser;
use Solarium\QueryType\Select\Result\Result;
use Solarium\QueryType\Update\Query\Document;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'response' => [
                'docs' => [
                    ['fieldA' => 1, 'fieldB' => 'Test'],
                    ['fieldA' => 2, 'fieldB' => 'Test2'],
                ],
                'numFound' => 503,
                'maxScore' => 1.23,
            ],
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
        ];

        $query = new Query(['documentclass' => Document::class]);
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->willReturn($data);
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertSame(503, $result['numfound']);
        $this->assertSame(1.23, $result['maxscore']);

        $docs = [
            new Document(['fieldA' => 1, 'fieldB' => 'Test']),
            new Document(['fieldA' => 2, 'fieldB' => 'Test2']),
        ];
        $this->assertEquals($docs, $result['documents']);

        $components = [
            Query::COMPONENT_FACETSET => new FacetSet([]),
        ];
        $this->assertEquals($components, $result['components']);
    }

    public function testParseWithoutScore()
    {
        $data = [
            'response' => [
                'docs' => [
                    ['fieldA' => 1, 'fieldB' => 'Test'],
                    ['fieldA' => 2, 'fieldB' => 'Test2'],
                ],
                'numFound' => 503,
            ],
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
        ];

        $query = new Query(['documentclass' => Document::class]);
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->willReturn($data);
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertSame(503, $result['numfound']);
        $this->assertNull($result['maxscore']);

        $docs = [
            new Document(['fieldA' => 1, 'fieldB' => 'Test']),
            new Document(['fieldA' => 2, 'fieldB' => 'Test2']),
        ];
        $this->assertEquals($docs, $result['documents']);

        $components = [
            Query::COMPONENT_FACETSET => new FacetSet([]),
        ];
        $this->assertEquals($components, $result['components']);
    }

    public function testParseWithInvalidDocumentClass()
    {
        $data = [
            'response' => [
                'docs' => [
                    ['fieldA' => 1, 'fieldB' => 'Test'],
                    ['fieldA' => 2, 'fieldB' => 'Test2'],
                ],
                'numFound' => 503,
            ],
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
        ];

        $query = new Query(['documentclass' => 'StdClass']);
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->willReturn($data);
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->willReturn($query);

        $parser = new ResponseParser();

        $this->expectException(RuntimeException::class);
        $parser->parse($resultStub);
    }

    public function testParseWithoutNumFound()
    {
        $data = [
            'response' => [
                'docs' => [
                    ['fieldA' => 1, 'fieldB' => 'Test'],
                    ['fieldA' => 2, 'fieldB' => 'Test2'],
                ],
            ],
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
        ];

        $query = new Query(['documentclass' => Document::class]);
        $query->getFacetSet();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->willReturn($data);
        $resultStub->expects($this->once())
             ->method('getQuery')
             ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertNull($result['numfound']);
    }
}
