<?php

namespace Solarium\Tests\QueryType\Luke\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\Result\Doc\DocInfo;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Info\Info;
use Solarium\QueryType\Luke\Result\Result;
use Solarium\QueryType\Luke\Result\Schema\Schema;
use Solarium\Tests\QueryType\Luke\ResponseParser\DocDataTrait;
use Solarium\Tests\QueryType\Luke\ResponseParser\FieldsDataTrait;
use Solarium\Tests\QueryType\Luke\ResponseParser\IndexDataTrait;
use Solarium\Tests\QueryType\Luke\ResponseParser\InfoDataTrait;
use Solarium\Tests\QueryType\Luke\ResponseParser\SchemaDataTrait;

class ResultTest extends TestCase
{
    use DocDataTrait;
    use FieldsDataTrait;
    use IndexDataTrait;
    use InfoDataTrait;
    use SchemaDataTrait;

    public function testWithShowAll(): void
    {
        $query = new Query();
        $query->setShow(Query::SHOW_ALL);

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'fields' => $this->getFieldsJsonData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertNull($result->getDoc());
        $this->assertContainsOnlyInstancesOf(FieldInfo::class, $result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithShowIndex(): void
    {
        $query = new Query();
        $query->setShow(Query::SHOW_INDEX);

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertNull($result->getDoc());
        $this->assertNull($result->getFields());
        $this->assertNull($result->getInfo());
    }

    public function testWithShowSchema(): void
    {
        $query = new Query();
        $query->setShow(Query::SHOW_SCHEMA);

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'schema' => $this->getSchemaData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertInstanceOf(Schema::class, $result->getSchema());
        $this->assertNull($result->getDoc());
        $this->assertNull($result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithShowDocWithoutIdOrDocId(): void
    {
        $query = new Query();
        $query->setShow(Query::SHOW_DOC);

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'fields' => $this->getFieldsJsonData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertNull($result->getDoc());
        $this->assertContainsOnlyInstancesOf(FieldInfo::class, $result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithShowDocWithId(): void
    {
        $query = new Query();
        $query->setShow(Query::SHOW_DOC);
        $query->setId('NCC-1701');

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'doc' => $this->getDocJsonData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertInstanceOf(DocInfo::class, $result->getDoc());
        $this->assertNull($result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithShowDocWithDocId(): void
    {
        $query = new Query();
        $query->setShow(Query::SHOW_DOC);
        $query->setDocId(1701);

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'doc' => $this->getDocJsonData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertInstanceOf(DocInfo::class, $result->getDoc());
        $this->assertNull($result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithoutShowWithoutIdOrDocId(): void
    {
        $query = new Query();

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'fields' => $this->getFieldsJsonData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertNull($result->getDoc());
        $this->assertContainsOnlyInstancesOf(FieldInfo::class, $result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithoutShowDocWithId(): void
    {
        $query = new Query();
        $query->setId('NCC-1701');

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'doc' => $this->getDocJsonData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertInstanceOf(DocInfo::class, $result->getDoc());
        $this->assertNull($result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithoutShowDocWithDocId(): void
    {
        $query = new Query();
        $query->setDocId(1701);

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'doc' => $this->getDocJsonData(),
            'info' => $this->getInfoData(),
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        $result = new Result($query, $response);

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertInstanceOf(DocInfo::class, $result->getDoc());
        $this->assertNull($result->getFields());
        $this->assertInstanceOf(Info::class, $result->getInfo());
    }

    public function testWithUnknownShow(): void
    {
        $query = new Query();
        $query->setShow('unknown');

        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'unknown' => [
                'hypothetical response data',
                'for a new "show" parameter',
                'that we don\'t support yet',
            ],
        ];

        $response = new Response(json_encode($data), ['HTTP/1.1 200 OK']);
        // get around deprecation for creation of dynamic property
        $result = new class($query, $response) extends Result {
            protected array $unknown;
        };

        $this->assertInstanceOf(Index::class, $result->getIndex());
        $this->assertNull($result->getSchema());
        $this->assertNull($result->getDoc());
        $this->assertNull($result->getFields());
        $this->assertNull($result->getInfo());
    }
}
