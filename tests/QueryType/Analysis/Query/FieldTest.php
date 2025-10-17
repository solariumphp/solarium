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

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_ANALYSIS_FIELD, $this->query->getType());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf('Solarium\QueryType\Analysis\ResponseParser\Field', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf('Solarium\QueryType\Analysis\RequestBuilder\Field', $this->query->getRequestBuilder());
    }

    public function testSetAndGetFieldValue(): void
    {
        $data = 'testdata';
        $this->query->setFieldValue($data);
        $this->assertSame($data, $this->query->getFieldValue());
    }

    public function testSetAndGetFieldType(): void
    {
        $data = 'testdata';
        $this->query->setFieldType($data);
        $this->assertSame($data, $this->query->getFieldType());
    }

    public function testSetAndGetFieldName(): void
    {
        $data = 'testdata';
        $this->query->setFieldName($data);
        $this->assertSame($data, $this->query->getFieldName());
    }
}
