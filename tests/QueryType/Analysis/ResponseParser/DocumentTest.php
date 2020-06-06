<?php

namespace Solarium\Tests\QueryType\Analysis\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Analysis\ResponseParser\Document;

class DocumentTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'analysis' => [
                'key1' => ['data1'],
                'key2' => ['data2'],
            ],
            'responseHeader' => [
                'status' => 1,
                'QTime' => 5,
            ],
        ];

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->willReturn($data);

        $parserStub = $this->getMockBuilder(Document::class)
            ->onlyMethods(['parseTypes'])
            ->getMock();
        $parserStub->expects($this->exactly(2))
             ->method('parseTypes')
             ->willReturn(['dummy']);

        $result = $parserStub->parse($resultStub);

        $this->assertCount(count($data['analysis']), $result['items']);
        $this->assertSame('key2', $result['items'][1]->getName());
    }
}
