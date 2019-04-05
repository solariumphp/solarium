<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\CoreAdmin\Query\Query;
use Solarium\QueryType\Server\Query\RequestBuilder;

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
        $reload = $this->query->createReload();
        $reload->setCore('foobar');
        $this->query->setAction($reload);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'core' => 'foobar',
                'wt' => 'json',
                'json.nl' => 'flat',
                'action' => 'RELOAD',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('admin/cores?wt=json&json.nl=flat&action=RELOAD&core=foobar', $request->getUri());
    }

    public function testCreate()
    {
        $create = $this->query->createCreate();
        $create->setCore('myNewCore');
        $create->setConfigSet('my_default_configSet');
        $create->setCoreProperty('mycustomproperty', 'somethingspecial');
        $this->query->setAction($create);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=CREATE&name=myNewCore&configSet=my_default_configSet&'.
            'property.mycustomproperty=somethingspecial';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testStatus()
    {
        $status = $this->query->createStatus();
        $status->setCore('statusCore');
        $status->setIndexInfo(true);
        $this->query->setAction($status);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=STATUS&core=statusCore&indexInfo=true';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testReload()
    {
        $reload = $this->query->createReload();
        $reload->setCore('reloadMe');
        $this->query->setAction($reload);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=RELOAD&core=reloadMe';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testRename()
    {
        $rename = $this->query->createRename();
        $rename->setCore('oldCore');
        $rename->setOther('newCore');
        $this->query->setAction($rename);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=RENAME&core=oldCore&other=newCore';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testSwap()
    {
        $swap = $this->query->createSwap();
        $swap->setCore('swapSource');
        $swap->setOther('swapTarget');
        $swap->setAsync('myHandle');
        $this->query->setAction($swap);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=SWAP&core=swapSource&other=swapTarget&async=myHandle';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testUnload()
    {
        $unload = $this->query->createUnload();
        $unload->setCore('unloadMe');
        $unload->setDeleteDataDir(true);
        $this->query->setAction($unload);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=UNLOAD&core=unloadMe&deleteDataDir=true';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testMergeIndexesBySrcCore()
    {
        $mergeIndexes = $this->query->createMergeIndexes();
        $mergeIndexes->setCore('targetCore');
        $mergeIndexes->setSrcCore(['oldCoreA', 'oldCoreB']);
        $this->query->setAction($mergeIndexes);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=MERGEINDEXES&core=targetCore&srcCore=oldCoreA&srcCore=oldCoreB';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testMergeIndexesByIndexDir()
    {
        $mergeIndexes = $this->query->createMergeIndexes();
        $mergeIndexes->setCore('targetCore');
        $mergeIndexes->setIndexDir(['/pathA/index', '/pathB/index']);
        $this->query->setAction($mergeIndexes);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=MERGEINDEXES&core=targetCore&indexDir=%2FpathA%2Findex&indexDir=%2FpathB%2Findex';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testSplitIntoTargetCore()
    {
        $split = $this->query->createSplit();
        $split->setCore('splitMe');
        $split->setTargetCore(['splittedA', 'splittedB']);
        $split->setAsync('asyncKey');
        $this->query->setAction($split);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=SPLIT&core=splitMe&targetCore=splittedA&targetCore=splittedB&async=asyncKey';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testSplitIntoTargetPath()
    {
        $split = $this->query->createSplit();
        $split->setCore('splitMe');
        $split->setPath(['/corea/data', '/coreb/data']);
        $split->setAsync('asyncKey');
        $this->query->setAction($split);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=SPLIT&core=splitMe&path=%2Fcorea%2Fdata&path=%2Fcoreb%2Fdata&async=asyncKey';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testSplitByKey()
    {
        $split = $this->query->createSplit();
        $split->setCore('splitMe');
        $split->setSplitKey('country');
        $this->query->setAction($split);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=SPLIT&core=splitMe&split.key=country';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testRequestStatus()
    {
        $requestStatus = $this->query->createRequestStatus();
        $requestStatus->setRequestId('myAsyncIdentifier');
        $this->query->setAction($requestStatus);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=REQUESTSTATUS&requestid=myAsyncIdentifier';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testRequestRecovery()
    {
        $requestRecovery = $this->query->createRequestRecovery();
        $requestRecovery->setCore('coreToRecover');
        $this->query->setAction($requestRecovery);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/cores?wt=json&json.nl=flat&action=REQUESTRECOVERY&core=coreToRecover';
        $this->assertSame($expectedUri, $request->getUri());
    }
}
