<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\DocumentInterface;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\ResponseParser\Doc as ResponseParser;
use Solarium\QueryType\Luke\Result\Doc\DocFieldInfo;
use Solarium\QueryType\Luke\Result\Doc\DocInfo;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Info\Info;
use Solarium\QueryType\Luke\Result\Result;

class DocTest extends TestCase
{
    use DocDataTrait;
    use IndexDataTrait;
    use InfoDataTrait;

    /**
     * @var Result|MockObject
     */
    protected $resultStub;

    public function setUp(): void
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'doc' => $this->getDocData(),
            'info' => $this->getInfoData(),
        ];

        // the doc parser accesses the response body directly
        $rawData = sprintf(<<<'JSON'
                {
                    "responseHeader": {
                        "status": 0,
                        "QTime": 3
                    },
                    "index": %s,
                    "doc": %s,
                    "info": %s
                }
            JSON,
            json_encode($data['index']),
            $this->getRawDocData(),
            json_encode($data['info']),
        );

        $responseStub = $this->createMock(Response::class);
        $responseStub->expects($this->any())
            ->method('getBody')
            ->willReturn($rawData);

        $this->resultStub = $this->createMock(Result::class);
        $this->resultStub->expects($this->any())
            ->method('getResponse')
            ->willReturn($responseStub);
        $this->resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
    }

    public function testParse(): DocInfo
    {
        $query = new Query();
        $query->setShow(Query::SHOW_DOC);
        $query->setDocId(1701);

        $this->resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($this->resultStub);

        $this->assertInstanceOf(Index::class, $result['indexResult']);
        $this->assertNull($result['schemaResult']);
        $this->assertInstanceOf(DocInfo::class, $result['docResult']);
        $this->assertNull($result['fieldsResult']);
        $this->assertInstanceOf(Info::class, $result['infoResult']);

        return $result['docResult'];
    }

    /**
     * @depends testParse
     */
    public function testDocId(DocInfo $doc)
    {
        $this->assertSame(1701, $doc->getDocId());
    }

    /**
     * @depends testParse
     */
    public function testLucene(DocInfo $doc)
    {
        $lucene = $doc->getLucene();

        // 5 single value fields + 1 multiValued with 2 values
        $this->assertCount(7, $lucene);
        $this->assertContainsOnlyInstancesOf(DocFieldInfo::class, $lucene);

        // single value field
        $this->assertSame('id', $lucene[0]->getName());
        $this->assertSame('string', $lucene[0]->getType());
        $this->assertSame('I-S-U-----OF-----l', (string) ($schema = $lucene[0]->getSchema()));
        $this->assertSame('ITS-------OF------', (string) ($flags = $lucene[0]->getFlags()));
        $this->assertSame('NCC-1701', $lucene[0]->getValue());
        $this->assertSame('NCC-1701', $lucene[0]->getInternal());
        $this->assertSame(1, $lucene[0]->getDocFreq());
        $this->assertNull($lucene[0]->getBinary());
        $this->assertNull($lucene[0]->getTermVector());

        // flags are covered exhaustively in SchemaTest::testFieldFlags()
        $this->assertFalse($schema->isTokenized());
        $this->assertTrue($schema->isSortMissingLast());
        $this->assertTrue($flags->isTokenized());
        $this->assertFalse($flags->isSortMissingLast());

        // 'value' is different from 'internal' for some field types
        $this->assertSame('true', $lucene[5]->getValue());
        $this->assertSame('T', $lucene[5]->getInternal());

        // 'value' and 'binary' are returned for binary fields, but 'internal' is null
        $this->assertTrue($lucene[6]->getFlags()->isBinary());
        $this->assertSame('PS9cPQ==', $lucene[6]->getValue());
        $this->assertSame('PS9cPQ==', $lucene[6]->getBinary());
        $this->assertNull($lucene[6]->getInternal());

        // some field types don't return 'docFreq'
        $this->assertNull($lucene[4]->getDocFreq());

        // 'termVector' is returned if Solr maintains full term vectors for a field
        $this->assertSame(
            [
                'enterprise' => 2,
                'document' => 1,
            ],
            $lucene[1]->getTermVector()
        );

        // multiValued field
        $this->assertSame('cat', $lucene[2]->getName());
        $this->assertSame('string', $lucene[2]->getType());
        $this->assertSame('Constitution', $lucene[2]->getValue());
        $this->assertSame('Constitution', $lucene[2]->getInternal());
        $this->assertSame(12, $lucene[2]->getDocFreq());
        $this->assertSame('cat', $lucene[3]->getName());
        $this->assertSame('string', $lucene[3]->getType());
        $this->assertSame('Galaxy', $lucene[3]->getValue());
        $this->assertSame('Galaxy', $lucene[3]->getInternal());
        $this->assertSame(6, $lucene[3]->getDocFreq());
    }

    /**
     * @depends testParse
     */
    public function testSolr(DocInfo $doc)
    {
        $solr = $doc->getSolr();

        $this->assertInstanceOf(DocumentInterface::class, $solr);

        $this->assertSame(
            [
                'id' => 'NCC-1701',
                'name' => 'Enterprise document',
                'cat' => [
                    'Constitution',
                    'Galaxy',
                ],
                'price' => 3.59,
                'flagship' => true,
                'insignia' => 'PS9cPQ==',
            ],
            $solr->getFields()
        );
    }

    public function testParseWithInvalidDocumentClass()
    {
        $query = new Query();
        $query->setDocumentClass(\stdClass::class);
        $query->setShow(Query::SHOW_DOC);
        $query->setDocId(1701);

        $this->resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();

        $this->expectException(RuntimeException::class);
        $parser->parse($this->resultStub);
    }
}
