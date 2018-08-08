<?php

namespace Solarium\Tests\QueryType\Extract;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Extract\Query;
use Solarium\QueryType\Extract\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new Query();
        $this->query->setFile(__FILE__);
        $this->query->addParam('param1', 'value1');
        $this->query->addFieldMapping('from-field', 'to-field');
        $this->builder = new RequestBuilder();
    }

    public function testGetMethod()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            Request::METHOD_POST,
            $request->getMethod()
        );
    }

    public function testGetFileUpload()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            __FILE__,
            $request->getFileUpload()
        );
    }

    public function testGetUri()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            'update/extract?omitHeader=true&param1=value1&wt=json&json.nl=flat&extractOnly=false&fmap.from-field=to-field'.
            '&resource.name=RequestBuilderTest.php',
            $request->getUri()
        );
    }

    public function testGetUriWithStreamUrl()
    {
        $query = $this->query;
        $query->setFile('http://solarium-project.org/');
        $request = $this->builder->build($query);
        $this->assertSame(
            'update/extract?omitHeader=true&param1=value1&wt=json&json.nl=flat&extractOnly=false&fmap.from-field=to-field'.
            '&stream.url=http%3A%2F%2Fsolarium-project.org%2F',
            $request->getUri()
        );
    }

    public function testDocumentFieldAndBoostParams()
    {
        $fields = ['field1' => 'value1', 'field2' => 'value2'];
        $boosts = ['field1' => 1, 'field2' => 5];
        $document = $this->query->createDocument($fields, $boosts);
        $this->query->setDocument($document);

        $request = $this->builder->build($this->query);
        $this->assertEquals(
            [
                'boost.field1' => 1,
                'boost.field2' => 5,
                'fmap.from-field' => 'to-field',
                'literal.field1' => 'value1',
                'literal.field2' => 'value2',
                'omitHeader' => 'true',
                'extractOnly' => 'false',
                'param1' => 'value1',
                'resource.name' => 'RequestBuilderTest.php',
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
    }

    public function testDocumentWithBoostThrowsException()
    {
        $document = $this->query->createDocument();
        $document->setBoost(4);
        $this->query->setDocument($document);

        $this->expectException('Solarium\Exception\RuntimeException');
        $this->builder->build($this->query);
    }

    public function testContentTypeHeader()
    {
        $request = $this->builder->build($this->query);
        $headers = [
            'Content-Type: multipart/form-data; boundary='.$request->getHash(),
        ];

        $this->assertSame($headers, $request->getHeaders());
    }

    public function testDocumentDateTimeField()
    {
        $timezone = new \DateTimeZone('Europe/London');
        $date = new \DateTime('2013-01-15 14:41:58', $timezone);

        $document = $this->query->createDocument(['date' => $date]);
        $this->query->setDocument($document);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'fmap.from-field' => 'to-field',
                'literal.date' => '2013-01-15T14:41:58Z',
                'omitHeader' => 'true',
                'extractOnly' => 'false',
                'param1' => 'value1',
                'resource.name' => 'RequestBuilderTest.php',
                'wt' => 'json',
                'json.nl' => 'flat',
            ],
            $request->getParams()
        );
    }
}
