<?php

namespace Solarium\Tests\QueryType\Luke\Result\Doc;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Doc\DocFieldInfo;
use Solarium\QueryType\Luke\Result\Doc\DocInfo;
use Solarium\QueryType\Select\Result\Document;

class DocInfoTest extends TestCase
{
    /**
     * @var DocInfo
     */
    protected $docInfo;

    public function setUp(): void
    {
        $this->docInfo = new DocInfo(42);
    }

    public function testGetDocId()
    {
        $this->assertSame(42, $this->docInfo->getDocId());
    }

    public function testSetAndGetLucene()
    {
        $docFieldInfo = new DocFieldInfo('field');
        $this->assertSame($this->docInfo, $this->docInfo->setLucene([$docFieldInfo]));
        $this->assertSame([$docFieldInfo], $this->docInfo->getLucene());
    }

    public function testSetAndGetSolr()
    {
        $document = new Document([]);
        $this->assertSame($this->docInfo, $this->docInfo->setSolr($document));
        $this->assertSame($document, $this->docInfo->getSolr());
    }
}
