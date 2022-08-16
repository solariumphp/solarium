<?php

namespace Solarium\Tests\QueryType\Server\Configsets;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Configsets\Query\Query;
use Solarium\QueryType\Server\Configsets\RequestBuilder;

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
        $this->builder = new RequestBuilder();
    }

    public function testBuildParams()
    {
        $action = $this->query->createList();

        $this->query->setAction($action);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
                'action' => 'LIST',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('admin/configs?wt=json&json.nl=flat&action=LIST', $request->getUri());
    }

    public function testCreate()
    {
        $create = $this->query->createCreate();
        $create->setName('someconfigset');
        $create->setBaseConfigSet('anotherconfigset');
        $create->setProperty('foo', 'bar');
        $this->query->setAction($create);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/configs?wt=json&json.nl=flat'.
            '&action=CREATE'.
            '&name=someconfigset'.
            '&baseConfigSet=anotherconfigset'.
            '&configSetProp.foo=bar';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testDelete()
    {
        $delete = $this->query->createDelete();
        $delete->setName('someconfigset');
        $this->query->setAction($delete);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/configs?wt=json&json.nl=flat'.
            '&action=DELETE'.
            '&name=someconfigset';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testUpload()
    {
        $reload = $this->query->createUpload();
        $reload->setName('someconfigset');
        $reload->setOverwrite(true);
        $reload->setFile(__FILE__);
        $reload->setFilePath('path/to/file');
        $reload->setCleanup(true);
        $reload->setOverwrite(true);
        $this->query->setAction($reload);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
        $expectedUri = 'admin/configs?wt=json&json.nl=flat'.
            '&action=UPLOAD'.
            '&name=someconfigset'.
            '&overwrite=true'.
            '&filePath=path%2Fto%2Ffile'.
            '&cleanup=true';
        $this->assertSame($expectedUri, $request->getUri());
        $this->assertSame(Request::CONTENT_TYPE_MULTIPART_FORM_DATA, $request->getContentType());
        $this->assertSame(['boundary' => $request->getHash()], $request->getContentTypeParams());
        $this->assertSame(__FILE__, $request->getFileUpload());
    }
}
