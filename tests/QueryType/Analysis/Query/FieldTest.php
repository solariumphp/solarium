<?php

namespace Solarium\Tests\QueryType\Analysis\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Analysis\Query\Field;

class FieldTest extends TestCase
{
    /**
     * @var Field
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Field();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_ANALYSIS_FIELD, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Analysis\ResponseParser\Field', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Analysis\RequestBuilder\Field', $this->query->getRequestBuilder());
    }

    public function testSetAndGetFieldValue()
    {
        $data = 'testdata';
        $this->query->setFieldValue($data);
        $this->assertSame($data, $this->query->getFieldValue());
    }

    public function testSetAndGetFieldType()
    {
        $data = 'testdata';
        $this->query->setFieldType($data);
        $this->assertSame($data, $this->query->getFieldType());
    }

    public function testSetAndGetFieldName()
    {
        $data = 'testdata';
        $this->query->setFieldName($data);
        $this->assertSame($data, $this->query->getFieldName());
    }
}
