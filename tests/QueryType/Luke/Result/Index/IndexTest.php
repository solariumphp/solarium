<?php

namespace Solarium\Tests\QueryType\Luke\Result\Index;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Index\UserData;

class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    protected $index;

    public function setUp(): void
    {
        $this->index = new Index();
    }

    public function testSetAndGetNumDocs()
    {
        $this->assertSame($this->index, $this->index->setNumDocs(20));
        $this->assertSame(20, $this->index->getNumDocs());
    }

    public function testSetAndGetMaxDoc()
    {
        $this->assertSame($this->index, $this->index->setMaxDoc(30));
        $this->assertSame(30, $this->index->getMaxDoc());
    }

    public function testSetAndGetDeletedDocs()
    {
        $this->assertSame($this->index, $this->index->setDeletedDocs(10));
        $this->assertSame(10, $this->index->getDeletedDocs());
    }

    public function testSetAndGetIndexHeapUsageBytes()
    {
        $this->assertSame($this->index, $this->index->setIndexHeapUsageBytes(5000));
        $this->assertSame(5000, $this->index->getIndexHeapUsageBytes());

        $this->assertSame($this->index, $this->index->setIndexHeapUsageBytes(null));
        $this->assertNull($this->index->getIndexHeapUsageBytes());
    }

    public function testSetAndGetVersion()
    {
        $this->assertSame($this->index, $this->index->setVersion(5));
        $this->assertSame(5, $this->index->getVersion());
    }

    public function testSetAndGetSegmentCount()
    {
        $this->assertSame($this->index, $this->index->setSegmentCount(1));
        $this->assertSame(1, $this->index->getSegmentCount());
    }

    public function testSetAndGetAndIsCurrent()
    {
        $this->assertSame($this->index, $this->index->setCurrent(true));
        $this->assertTrue($this->index->getCurrent());
        $this->assertTrue($this->index->isCurrent());
    }

    public function testSetAndGetAndHasDeletions()
    {
        $this->assertSame($this->index, $this->index->setHasDeletions(false));
        $this->assertFalse($this->index->getHasDeletions());
        $this->assertFalse($this->index->hasDeletions());
    }

    public function testSetAndGetDirectory()
    {
        $this->assertSame($this->index, $this->index->setDirectory('directory info'));
        $this->assertSame('directory info', $this->index->getDirectory());
    }

    public function testSetAndGetSegmentsFile()
    {
        $this->assertSame($this->index, $this->index->setSegmentsFile('segments_2'));
        $this->assertSame('segments_2', $this->index->getSegmentsFile());
    }

    public function testSetAndGetSegmentsFileSizeInBytes()
    {
        $this->assertSame($this->index, $this->index->setSegmentsFileSizeInBytes(200));
        $this->assertSame(200, $this->index->getSegmentsFileSizeInBytes());
    }

    public function testSetAndGetUserData()
    {
        $userData = new UserData();
        $this->assertSame($this->index, $this->index->setUserData($userData));
        $this->assertSame($userData, $this->index->getUserData());
    }

    public function testSetAndGetLastModified()
    {
        $lastModified = new \DateTime();
        $this->assertSame($this->index, $this->index->setLastModified($lastModified));
        $this->assertSame($lastModified, $this->index->getLastModified());

        $this->assertSame($this->index, $this->index->setLastModified(null));
        $this->assertNull($this->index->getLastModified());
    }
}
