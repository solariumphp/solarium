<?php

namespace Solarium\Tests\QueryType\Extract;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Extract\Query;
use Solarium\QueryType\Extract\ResponseParser;
use Solarium\QueryType\Extract\Result;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 15,
            ],
            'file' => 'dummy data',
            'file_metadata' => [
                'foo',
                'bar',
            ],
        ];

        $data['document.pdf'] = &$data['file'];
        $data['document.pdf_metadata'] = &$data['file_metadata'];

        $query = new Query();
        $query->setExtractOnly(true);
        $query->setResourceName('document.pdf');
        $query->setResponseWriter($query::WT_JSON);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();
        $parsed = $parser->parse($resultStub);

        $this->assertSame('dummy data', $parsed['file']);
        $this->assertSame(['foo' => 'bar'], $parsed['fileMetadata']);
    }
}
