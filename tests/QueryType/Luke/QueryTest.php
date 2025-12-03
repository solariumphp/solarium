<?php

namespace Solarium\Tests\QueryType\Luke;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\RequestBuilder;
use Solarium\QueryType\Luke\ResponseParser\Doc as DocResponseParser;
use Solarium\QueryType\Luke\ResponseParser\Index as IndexResponseParser;
use Solarium\QueryType\Luke\ResponseParser\Fields as FieldsResponseParser;
use Solarium\QueryType\Luke\ResponseParser\Schema as SchemaResponseParser;

class QueryTest extends TestCase
{
    protected Query $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testConfigMode(): void
    {
        $options = [
            'handler' => 'my/handler',
            'resultclass' => 'MyResult',
            'documentclass' => 'MyDocument',
            'omitheader' => false,
            'show' => Query::SHOW_INDEX,
            'id' => 'abc',
            'docId' => 123,
            'fields' => ['field1', 'field2'],
            'numTerms' => 5,
            'includeIndexFieldFlags' => true,
        ];
        $this->query->setOptions($options);

        $this->assertSame(
            $options['handler'],
            $this->query->getHandler()
        );

        $this->assertSame(
            $options['resultclass'],
            $this->query->getResultClass()
        );

        $this->assertSame(
            $options['documentclass'],
            $this->query->getDocumentClass()
        );

        $this->assertFalse(
            $this->query->getOmitHeader()
        );

        $this->assertSame(
            $options['show'],
            $this->query->getShow()
        );

        $this->assertSame(
            $options['id'],
            $this->query->getId()
        );

        $this->assertSame(
            $options['docId'],
            $this->query->getDocId()
        );

        $this->assertSame(
            $options['fields'],
            $this->query->getFields()
        );

        $this->assertSame(
            $options['numTerms'],
            $this->query->getNumTerms()
        );

        $this->assertTrue(
            $this->query->getIncludeIndexFieldFlags()
        );
    }

    public function testConfigModeFieldsAsString(): void
    {
        $this->query->setOptions(['fields' => 'field3,field4, field5']);
        $this->assertSame(
            ['field3', 'field4', 'field5'],
            $this->query->getFields()
        );
    }

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_LUKE, $this->query->getType());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    /**
     * Without parameters, the behaviour is the same as SHOW_ALL.
     */
    public function testGetResponseParserWithoutShowWithoutFieldsOrIdOrDocId(): void
    {
        $this->assertInstanceOf(FieldsResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * With only 'fl', the same parsing as for SHOW_ALL can be used.
     */
    public function testGetResponseParserWithoutShowWithFields(): void
    {
        $this->query->setFields('field1,field2');
        $this->assertInstanceOf(FieldsResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * With only an 'id', the behaviour is the same as SHOW_DOC with 'id'.
     */
    public function testGetResponseParserWithoutShowWithId(): void
    {
        $this->query->setId('abc');
        $this->assertInstanceOf(DocResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * With only a 'docId', the behaviour is the same as SHOW_DOC with 'docId'.
     */
    public function testGetResponseParserWithoutShowWithDocId(): void
    {
        $this->query->setDocId(123);
        $this->assertInstanceOf(DocResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithShowAll(): void
    {
        $this->query->setShow(Query::SHOW_ALL);
        $this->assertInstanceOf(FieldsResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithShowAllWithFields(): void
    {
        $this->query->setShow(Query::SHOW_ALL);
        $this->query->setFields('field1,field2');
        $this->assertInstanceOf(FieldsResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * SHOW_DOC without 'id' or 'docId' behaves like SHOW_ALL.
     */
    public function testGetResponseParserWithShowDocWithoutIdOrDocId(): void
    {
        $this->query->setShow(Query::SHOW_DOC);
        $this->assertInstanceOf(FieldsResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * SHOW_DOC without 'id' or 'docId', but with 'fl', behaves like 'fl' without 'show'.
     */
    public function testGetResponseParserWithShowDocWithOnlyFields(): void
    {
        $this->query->setShow(Query::SHOW_DOC);
        $this->query->setFields('field1,field2');
        $this->assertInstanceOf(FieldsResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithShowDocWithId(): void
    {
        $this->query->setShow(Query::SHOW_DOC);
        $this->query->setId('abc');
        $this->assertInstanceOf(DocResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * 'fl' is ignored if SHOW_DOC has an 'id'.
     */
    public function testGetResponseParserWithShowDocWithIdAndFields(): void
    {
        $this->query->setShow(Query::SHOW_DOC);
        $this->query->setId('abc');
        $this->query->setFields('field1,field2');
        $this->assertInstanceOf(DocResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithShowDocWithDocId(): void
    {
        $this->query->setShow(Query::SHOW_DOC);
        $this->query->setDocId(123);
        $this->assertInstanceOf(DocResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * 'fl' is ignored if SHOW_DOC has a 'docId'.
     */
    public function testGetResponseParserWithShowDocWithDocIdAndFields(): void
    {
        $this->query->setShow(Query::SHOW_DOC);
        $this->query->setDocId(123);
        $this->query->setFields('field1,field2');
        $this->assertInstanceOf(DocResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithShowIndex(): void
    {
        $this->query->setShow(Query::SHOW_INDEX);
        $this->assertInstanceOf(IndexResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithShowSchema(): void
    {
        $this->query->setShow(Query::SHOW_SCHEMA);
        $this->assertInstanceOf(SchemaResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * Fallback to SHOW_INDEX behaviour for unknown show style.
     */
    public function testGetResponseParserWithUnknownShow(): void
    {
        $this->query->setShow('unknown');
        $this->assertInstanceOf(IndexResponseParser::class, $this->query->getResponseParser());
    }

    public function testSetAndGetDocumentClass(): void
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertSame('MyDocument', $this->query->getDocumentClass());
    }

    public function testSetAndGetShow(): void
    {
        $this->query->setShow(Query::SHOW_SCHEMA);
        $this->assertSame(Query::SHOW_SCHEMA, $this->query->getShow());
    }

    public function testSetAndGetId(): void
    {
        $this->query->setId('abc');
        $this->assertSame('abc', $this->query->getId());
    }

    public function testSetAndGetDocId(): void
    {
        $this->query->setDocId(123);
        $this->assertSame(123, $this->query->getDocId());
    }

    public function testSetAndGetFieldsAsArray(): void
    {
        $this->query->setFields(['field1', 'field2']);
        $this->assertSame(['field1', 'field2'], $this->query->getFields());
    }

    public function testSetAndGetFieldsAsString(): void
    {
        $this->query->setFields('field1,field2, field3');
        $this->assertSame(['field1', 'field2', 'field3'], $this->query->getFields());
    }

    public function testGetUnsetFields(): void
    {
        $this->assertSame([], $this->query->getFields());
    }

    public function testSetAndGetNumTerms(): void
    {
        $this->query->setNumTerms(15);
        $this->assertSame(15, $this->query->getNumTerms());
    }

    public function testSetAndGetIncludeIndexFieldFlags(): void
    {
        $this->query->setIncludeIndexFieldFlags(false);
        $this->assertFalse($this->query->getIncludeIndexFieldFlags());
    }
}
