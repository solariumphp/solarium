<?php

namespace Solarium\Tests\QueryType\Luke\Result\Index;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Index\UserData;

class IndexTest extends TestCase
{
    protected Index $index;

    public function setUp(): void
    {
        $this->index = new Index();
    }

    public function testSetAndGetNumDocs(): void
    {
        $this->assertSame($this->index, $this->index->setNumDocs(20));
        $this->assertSame(20, $this->index->getNumDocs());
    }

    public function testSetAndGetMaxDoc(): void
    {
        $this->assertSame($this->index, $this->index->setMaxDoc(30));
        $this->assertSame(30, $this->index->getMaxDoc());
    }

    public function testSetAndGetDeletedDocs(): void
    {
        $this->assertSame($this->index, $this->index->setDeletedDocs(10));
        $this->assertSame(10, $this->index->getDeletedDocs());
    }

    public function testSetAndGetIndexHeapUsageBytes(): void
    {
        $this->assertSame($this->index, $this->index->setIndexHeapUsageBytes(5000));
        $this->assertSame(5000, $this->index->getIndexHeapUsageBytes());

        $this->assertSame($this->index, $this->index->setIndexHeapUsageBytes(null));
        $this->assertNull($this->index->getIndexHeapUsageBytes());
    }

    public function testSetAndGetVersion(): void
    {
        $this->assertSame($this->index, $this->index->setVersion(5));
        $this->assertSame(5, $this->index->getVersion());
    }

    public function testSetAndGetSegmentCount(): void
    {
        $this->assertSame($this->index, $this->index->setSegmentCount(1));
        $this->assertSame(1, $this->index->getSegmentCount());
    }

    public function testSetAndGetAndIsCurrent(): void
    {
        $this->assertSame($this->index, $this->index->setCurrent(true));
        $this->assertTrue($this->index->getCurrent());
        $this->assertTrue($this->index->isCurrent());
    }

    public function testSetAndGetAndHasDeletions(): void
    {
        $this->assertSame($this->index, $this->index->setHasDeletions(false));
        $this->assertFalse($this->index->getHasDeletions());
        $this->assertFalse($this->index->hasDeletions());
    }

    public function testSetAndGetDirectory(): void
    {
        $this->assertSame($this->index, $this->index->setDirectory('directory info'));
        $this->assertSame('directory info', $this->index->getDirectory());
    }

    public function testSetAndGetSegmentsFile(): void
    {
        $this->assertSame($this->index, $this->index->setSegmentsFile('segments_2'));
        $this->assertSame('segments_2', $this->index->getSegmentsFile());
    }

    public function testSetAndGetSegmentsFileSizeInBytes(): void
    {
        $this->assertSame($this->index, $this->index->setSegmentsFileSizeInBytes(200));
        $this->assertSame(200, $this->index->getSegmentsFileSizeInBytes());
    }

    public function testSetAndGetUserData(): void
    {
        $userData = new UserData();
        $this->assertSame($this->index, $this->index->setUserData($userData));
        $this->assertSame($userData, $this->index->getUserData());
    }

    public function testSetAndGetLastModified(): void
    {
        $lastModified = new \DateTime();
        $this->assertSame($this->index, $this->index->setLastModified($lastModified));
        $this->assertSame($lastModified, $this->index->getLastModified());

        $this->assertSame($this->index, $this->index->setLastModified(null));
        $this->assertNull($this->index->getLastModified());
    }
}
