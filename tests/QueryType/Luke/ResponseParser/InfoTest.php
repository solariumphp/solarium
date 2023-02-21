<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\ResponseParser\Fields as ResponseParser;
use Solarium\QueryType\Luke\Result\Info\Info;
use Solarium\QueryType\Luke\Result\Result;

class InfoTest extends TestCase
{
    use FieldsDataTrait;
    use IndexDataTrait;
    use InfoDataTrait;

    public function testParse(): Info
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'fields' => $this->getFieldsData(),
            'info' => $this->getInfoData(),
        ];

        $query = new Query();

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertInstanceOf(Info::class, $result['infoResult']);

        return $result['infoResult'];
    }

    /**
     * @depends testParse
     */
    public function testInfo(Info $info)
    {
        $infoData = $this->getInfoData();

        $this->assertSame($infoData['key'], $info->getKey());
        $this->assertSame($infoData['NOTE'], $info->getNote());
    }
}
