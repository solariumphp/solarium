<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\ResponseParser\Fields as ResponseParser;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Info\Info;
use Solarium\QueryType\Luke\Result\Result;

class FieldsTest extends TestCase
{
    use FieldsDataTrait;
    use IndexDataTrait;
    use InfoDataTrait;

    public function testParse(): array
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
        $query->setShow(Query::SHOW_ALL);
        $query->setFields('*');

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertInstanceOf(Index::class, $result['indexResult']);
        $this->assertNull($result['schemaResult']);
        $this->assertNull($result['docResult']);
        $this->assertContainsOnlyInstancesOf(FieldInfo::class, $result['fieldsResult']);
        $this->assertInstanceOf(Info::class, $result['infoResult']);

        return $result['fieldsResult'];
    }

    /**
     * @depends testParse
     */
    public function testName(array $fields)
    {
        $this->assertSame('field', $fields['field']->getName());
    }

    /**
     * @depends testParse
     */
    public function testType(array $fields)
    {
        $this->assertSame('type_a', $fields['field']->getType());
    }

    /**
     * @depends testParse
     */
    public function testSchema(array $fields)
    {
        $schema = $fields['field']->getSchema();

        $this->assertSame('I-S-U-----OF-----l', (string) $schema);

        // flags are covered exhaustively in SchemaTest::testFieldFlags()
        $this->assertTrue($schema->isIndexed());
        $this->assertFalse($schema->isTokenized());
    }

    /**
     * @depends testParse
     */
    public function testDynamicBase(array $fields)
    {
        $this->assertNull($fields['field']->getDynamicBase());
        $this->assertSame('*_field', $fields['dynamic_field']->getDynamicBase());
    }

    /**
     * @depends testParse
     */
    public function testIndex(array $fields)
    {
        $index = $fields['field']->getIndex();

        $this->assertSame('ITS-------OF------', (string) $index);

        // flags are covered exhaustively in SchemaTest::testFieldFlags()
        $this->assertTrue($index->isTokenized());
        $this->assertFalse($index->isUninvertible());

        $this->assertSame('(unstored field)', $fields['field_unstored']->getIndex());

        $this->assertNull($fields['field_unindexed']->getIndex());
    }

    /**
     * @depends testParse
     */
    public function testDocs(array $fields)
    {
        $this->assertSame(25, $fields['field']->getDocs());
        $this->assertNull($fields['field_unindexed']->getDocs());
    }

    /**
     * @depends testParse
     */
    public function testDistinct(array $fields)
    {
        $this->assertSame(70, $fields['field']->getDistinct());
        $this->assertNull($fields['field_unindexed']->getDistinct());
    }

    /**
     * @depends testParse
     */
    public function testTopTerms(array $fields)
    {
        $this->assertSame([
            'a' => 18,
            'b' => 7,
        ], $fields['field']->getTopTerms());
        $this->assertNull($fields['field_unindexed']->getTopTerms());
    }

    /**
     * @depends testParse
     */
    public function testHistogram(array $fields)
    {
        $this->assertSame([
            '1' => 0,
            '2' => 1,
            '4' => 2,
        ], $fields['field']->getHistogram());
        $this->assertNull($fields['field_unindexed']->getHistogram());
    }

    /**
     * @depends testParse
     */
    public function testToString(array $fields)
    {
        $this->assertSame('field', (string) $fields['field']);
    }
}
