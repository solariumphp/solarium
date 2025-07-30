<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

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

    public function testParseJson(): DocInfo
    {
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
            json_encode($this->getIndexData()),
            $this->getRawDocJsonData(),
            json_encode($this->getInfoData()),
        );

        $query = new Query();
        $query->setResponseWriter($query::WT_JSON);
        $query->setShow(Query::SHOW_DOC);
        $query->setDocId(1701);

        $response = new Response($rawData, ['HTTP/1.0 200 OK']);
        $result = new Result($query, $response);

        $parser = new ResponseParser();
        $data = $parser->parse($result);

        $this->assertInstanceOf(Index::class, $data['indexResult']);
        $this->assertNull($data['schemaResult']);
        $this->assertInstanceOf(DocInfo::class, $data['docResult']);
        $this->assertNull($data['fieldsResult']);
        $this->assertInstanceOf(Info::class, $data['infoResult']);

        return $data['docResult'];
    }

    /**
     * @depends testParseJson
     */
    public function testParsePhps(DocInfo $doc)
    {
        // the doc parser accesses the response body directly
        $rawData = sprintf(
            'a:4:{s:14:"responseHeader";a:2:{s:6:"status";i:0;s:5:"QTime";i:3;}s:5:"index";%ss:3:"doc";%ss:4:"info";%s}',
            serialize($this->getIndexData()),
            $this->getRawDocPhpsData(),
            serialize($this->getInfoData()),
        );

        $query = new Query();
        $query->setResponseWriter($query::WT_PHPS);
        $query->setShow(Query::SHOW_DOC);
        $query->setDocId(1701);

        $response = new Response($rawData, ['HTTP/1.0 200 OK']);
        $result = new Result($query, $response);

        $parser = new ResponseParser();
        $data = $parser->parse($result);

        $this->assertInstanceOf(Index::class, $data['indexResult']);
        $this->assertNull($data['schemaResult']);
        $this->assertEquals($doc, $data['docResult']);
        $this->assertNull($data['fieldsResult']);
        $this->assertInstanceOf(Info::class, $data['infoResult']);
    }

    /**
     * @depends testParseJson
     */
    public function testDocId(DocInfo $doc)
    {
        $this->assertSame(1701, $doc->getDocId());
    }

    /**
     * @depends testParseJson
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
     * @depends testParseJson
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
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'doc' => $this->getDocJsonData(),
            'info' => $this->getInfoData(),
        ];

        $query = new Query();
        $query->setResponseWriter($query::WT_JSON);
        $query->setDocumentClass(\stdClass::class);
        $query->setShow(Query::SHOW_DOC);
        $query->setDocId(1701);

        $response = new Response(json_encode($data), ['HTTP/1.0 200 OK']);
        $result = new Result($query, $response);

        $parser = new ResponseParser();

        $this->expectException(RuntimeException::class);
        $parser->parse($result);
    }
}
