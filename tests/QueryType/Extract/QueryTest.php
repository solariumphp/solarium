<?php

namespace Solarium\Tests\QueryType\Extract;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Extract\Query;
use Solarium\QueryType\Extract\RequestBuilder;
use Solarium\QueryType\Extract\ResponseParser;
use Solarium\QueryType\Update\Query\Document;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_EXTRACT, $this->query->getType());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }

    public function testConfigMode()
    {
        $mappings = [
            'from1' => 'to1',
            'from2' => 'to2',
        ];

        $options = [
            'fmap' => $mappings,
        ];

        $this->query->setOptions($options);

        $this->assertSame(
            $mappings,
            $this->query->getFieldMappings()
        );
    }

    public function testSetAndGetStart()
    {
        $doc = new Document(['field1', 'value1']);
        $this->query->setDocument($doc);
        $this->assertSame($doc, $this->query->getDocument());
    }

    public function testSetAndGetFilename()
    {
        $this->query->setFile(__FILE__);
        $this->assertSame(__FILE__, $this->query->getFile());
    }

    public function testSetAndGetFileUrl()
    {
        $this->query->setFile('http://solarium-project.org/');
        $this->assertSame('http://solarium-project.org/', $this->query->getFile());
    }

    public function testSetAndGetFileResource()
    {
        $file = fopen('php://memory', 'r');
        $this->query->setFile($file);
        $this->assertSame($file, $this->query->getFile());
        fclose($file);
    }

    public function testSetAndGetUprefix()
    {
        $this->query->setUprefix('dyn_');
        $this->assertSame('dyn_', $this->query->getUprefix());
    }

    public function testSetAndGetDefaultField()
    {
        $this->query->setDefaultField('defaulttext');
        $this->assertSame('defaulttext', $this->query->getDefaultField());
    }

    public function testSetAndGetExtractOnly()
    {
        $this->query->setExtractOnly(true);
        $this->assertTrue($this->query->getExtractOnly());
    }

    public function testSetAndGetExtractFormat()
    {
        $this->query->setExtractFormat(Query::EXTRACT_FORMAT_TEXT);
        $this->assertSame('text', $this->query->getExtractFormat());

        $this->query->setExtractFormat(Query::EXTRACT_FORMAT_XML);
        $this->assertSame('xml', $this->query->getExtractFormat());
    }

    public function testSetAndGetLowernames()
    {
        $this->query->setLowernames(true);
        $this->assertTrue($this->query->getLowernames());
    }

    public function testSetAndGetCommit()
    {
        $this->query->setCommit(true);
        $this->assertTrue($this->query->getCommit());
    }

    public function testSetAndGetCommitWithin()
    {
        $this->query->setCommitWithin(458);
        $this->assertSame(458, $this->query->getCommitWithin());
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('Solarium\Tests\QueryType\Extract\MyCustomDoc');
        $this->assertSame('Solarium\Tests\QueryType\Extract\MyCustomDoc', $this->query->getDocumentClass());

        return $this->query;
    }

    /**
     * @depends testSetAndGetDocumentClass
     *
     * @param mixed $query
     */
    public function testCreateDocument($query)
    {
        $fields = ['key1' => 'value1', 'key2' => 'value2'];
        $boosts = ['key1' => 1.0, 'key2' => 2.0];
        $document = $query->createDocument($fields, $boosts);

        $this->assertInstanceOf('Solarium\Tests\QueryType\Extract\MyCustomDoc', $document);
        $this->assertSame($boosts, $document->getFieldBoosts());
        $this->assertSame($fields, $document->getFields());
    }

    public function testAddFieldMapping()
    {
        $expectedFields = $this->query->getFieldMappings();
        $expectedFields['newfield'] = 'tofield';
        $this->query->addFieldMapping('newfield', 'tofield');
        $this->assertSame($expectedFields, $this->query->getFieldMappings());

        return $this->query;
    }

    /**
     * @depends testAddFieldMapping
     *
     * @param Query $query
     */
    public function testClearFieldMappingss($query)
    {
        $query->clearFieldMappings();
        $this->assertSame([], $query->getFieldMappings());

        return $query;
    }

    /**
     * @depends testClearFieldMappingss
     *
     * @param Query $query
     */
    public function testAddFieldMappings($query)
    {
        $fields = ['field1' => 'target1', 'field2' => 'target2'];
        $query->addFieldMappings($fields);
        $this->assertSame($fields, $query->getFieldMappings());

        return $query;
    }

    /**
     * @depends testAddFieldMappings
     *
     * @param Query $query
     */
    public function testRemoveFieldMapping($query)
    {
        $query->removeFieldMapping('field1');
        $this->assertSame(['field2' => 'target2'], $query->getFieldMappings());

        return $query;
    }

    /**
     * @depends testRemoveFieldMapping
     *
     * @param Query $query
     */
    public function testSetFields($query)
    {
        $fields = ['field3' => 'target3', 'field4' => 'target4'];
        $query->setFieldMappings($fields);
        $this->assertSame($fields, $query->getFieldMappings());
    }

    public function testSetAndGetResourceName()
    {
        $this->query->setResourceName('document.pdf');
        $this->assertSame('document.pdf', $this->query->getResourceName());
    }
}

class MyCustomDoc extends Document
{
}
