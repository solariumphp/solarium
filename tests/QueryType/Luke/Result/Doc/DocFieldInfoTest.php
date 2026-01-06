<?php

namespace Solarium\Tests\QueryType\Luke\Result\Doc;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Doc\DocFieldInfo;
use Solarium\QueryType\Luke\Result\FlagList;

class DocFieldInfoTest extends TestCase
{
    protected DocFieldInfo $docFieldInfo;

    public function setUp(): void
    {
        $this->docFieldInfo = new DocFieldInfo('fieldA');
    }

    public function testGetName(): void
    {
        $this->assertSame('fieldA', $this->docFieldInfo->getName());
    }

    public function testSetAndGetType(): void
    {
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setType('my_type'));
        $this->assertSame('my_type', $this->docFieldInfo->getType());

        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setType(null));
        $this->assertNull($this->docFieldInfo->getType());
    }

    public function testSetAndGetSchema(): void
    {
        $schema = new FlagList('-', []);
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setSchema($schema));
        $this->assertSame($schema, $this->docFieldInfo->getSchema());
    }

    public function testSetAndGetFlags(): void
    {
        $flags = new FlagList('-', []);
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setFlags($flags));
        $this->assertSame($flags, $this->docFieldInfo->getFlags());
    }

    public function testSetAndGetValue(): void
    {
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setValue('external value'));
        $this->assertSame('external value', $this->docFieldInfo->getValue());

        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setValue(null));
        $this->assertNull($this->docFieldInfo->getValue());
    }

    public function testSetAndGetInternal(): void
    {
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setInternal('internal value'));
        $this->assertSame('internal value', $this->docFieldInfo->getInternal());

        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setInternal(null));
        $this->assertNull($this->docFieldInfo->getInternal());
    }

    public function testSetAndGetBinary(): void
    {
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setBinary('binary value'));
        $this->assertSame('binary value', $this->docFieldInfo->getBinary());

        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setBinary(null));
        $this->assertNull($this->docFieldInfo->getBinary());
    }

    public function testSetAndGetDocFreq(): void
    {
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setDocFreq(5));
        $this->assertSame(5, $this->docFieldInfo->getDocFreq());

        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setDocFreq(null));
        $this->assertNull($this->docFieldInfo->getDocFreq());
    }

    public function testSetAndGetTermVector(): void
    {
        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setTermVector([]));
        $this->assertSame([], $this->docFieldInfo->getTermVector());

        $this->assertSame($this->docFieldInfo, $this->docFieldInfo->setTermVector(null));
        $this->assertNull($this->docFieldInfo->getTermVector());
    }

    public function testToString(): void
    {
        $this->assertSame('fieldA: ', (string) $this->docFieldInfo);

        $this->docFieldInfo->setValue('string representation of the value');
        $this->assertSame('fieldA: string representation of the value', (string) $this->docFieldInfo);
    }
}
