<?php

namespace Solarium\Tests\QueryType\Extract;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;
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

    public function setUp(): void
    {
        $this->query = new Query();
        $this->query->setFile(__FILE__);
        $this->query->addParam('param1', 'value1');
        $this->query->addFieldMapping('from-field', 'to-field');
        $this->builder = new RequestBuilder();
    }

    public function testGetMethodWithFileUpload()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            Request::METHOD_POST,
            $request->getMethod()
        );
    }

    public function testGetMethodWithStreamUrl()
    {
        $query = $this->query;
        $query->setFile('http://solarium-project.org/');
        $request = $this->builder->build($query);
        $this->assertSame(
            Request::METHOD_GET,
            $request->getMethod()
        );
    }

    public function testGetMethodWithResource()
    {
        $file = fopen('php://memory', 'w+');
        $query = $this->query;
        $query->setFile($file);
        $request = $this->builder->build($query);
        $this->assertSame(
            Request::METHOD_POST,
            $request->getMethod()
        );
        fclose($file);
    }

    public function testGetFileUpload()
    {
        $request = $this->builder->build($this->query);
        $this->assertSame(
            __FILE__,
            $request->getFileUpload()
        );
    }

    public function testGetFileUploadWithResource()
    {
        $file = fopen('php://memory', 'w+');
        $query = $this->query;
        $query->setFile($file);
        $request = $this->builder->build($query);
        $this->assertSame(
            $file,
            $request->getFileUpload()
        );
        fclose($file);
    }

    public function testSetsResourceNameWithFileUpload()
    {
        $this->builder->build($this->query);
        $this->assertSame(
            basename(__FILE__),
            $this->query->getResourceName()
        );
    }

    public function testSetsResourceNameWithStreamUrl()
    {
        $query = $this->query;
        $query->setFile('http://solarium-project.org/');
        $this->builder->build($query);
        $this->assertSame(
            'http://solarium-project.org/',
            $query->getResourceName()
        );
    }

    public function testSetsResourceNameWithResource()
    {
        $file = fopen('php://memory', 'w+');
        $query = $this->query;
        $query->setFile($file);
        $this->builder->build($query);
        $this->assertSame(
            'memory',
            $query->getResourceName()
        );
        fclose($file);
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

    public function testGetUriWithExtractFormat()
    {
        $query = $this->query;
        $query->setExtractOnly(true);
        $query->setExtractFormat($query::EXTRACT_FORMAT_TEXT);
        $request = $this->builder->build($query);
        $this->assertSame(
            'update/extract?omitHeader=true&param1=value1&wt=json&json.nl=flat&extractOnly=true&extractFormat=text&fmap.from-field=to-field'.
            '&resource.name=RequestBuilderTest.php',
            $request->getUri()
        );
    }

    public function testGetUriWithInputEncoding()
    {
        $query = $this->query;
        $query->setInputEncoding('iso-8859-1');
        $query->setFile(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Integration'.DIRECTORY_SEPARATOR.'Fixtures'.DIRECTORY_SEPARATOR.'test iso-8859-1 ¡¢£¤¥¦§¨©ª«¬.xml');
        $request = $this->builder->build($this->query);
        $this->assertSame(
            'update/extract?omitHeader=true&ie=iso-8859-1&param1=value1&wt=json&json.nl=flat&extractOnly=false&fmap.from-field=to-field'.
            '&resource.name=test+iso-8859-1+%A1%A2%A3%A4%A5%A6%A7%A8%A9%AA%AB%AC.xml',
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

    public function testGetUriWithResource()
    {
        $file = fopen('php://memory', 'w+');
        $query = $this->query;
        $query->setFile($file);
        $request = $this->builder->build($query);
        $this->assertSame(
            'update/extract?omitHeader=true&param1=value1&wt=json&json.nl=flat&extractOnly=false&fmap.from-field=to-field'.
            '&resource.name=memory',
            $request->getUri()
        );
        fclose($file);
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

        $this->expectException(RuntimeException::class);
        $this->builder->build($this->query);
    }

    public function testContentTypeHeader()
    {
        $request = $this->builder->build($this->query);

        $this->assertSame(Request::CONTENT_TYPE_MULTIPART_FORM_DATA, $request->getContentType());
        $this->assertSame(['boundary' => $request->getHash()], $request->getContentTypeParams());
    }

    public function testDocumentDateTimeField()
    {
        $timezone = new \DateTimeZone('+07:30');
        $date = new \DateTime('2013-01-15 14:41:58', $timezone);

        $document = $this->query->createDocument(['date' => $date]);
        $this->query->setDocument($document);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'fmap.from-field' => 'to-field',
                'literal.date' => '2013-01-15T07:11:58Z',
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

    public function testInvalidFileThrowsException()
    {
        $query = new Query();
        $query->setFile('nosuchfile');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Extract query file path/url invalid or not available: nosuchfile');

        $this->builder->build($query);
    }
}
