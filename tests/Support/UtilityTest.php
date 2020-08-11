<?php

namespace Solarium\Tests\Support;

use PHPUnit\Framework\TestCase;
use Solarium\Support\Utility;

class UtilityTest extends TestCase
{
    /**
     * @var string
     */
    protected $fixtures;

    public function setUp(): void
    {
        $this->fixtures = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Integration'.DIRECTORY_SEPARATOR.'Fixtures');
    }

    public function testGetXmlEncodingNoFile()
    {
        $this->expectError();
        $this->assertNull(
            Utility::getXmlEncoding('nosuchfile')
        );
    }

    public function testGetXmlEncodingWithoutUtf8BomWithoutXmlDeclaration()
    {
        $this->assertNull(
            Utility::getXmlEncoding($this->fixtures.DIRECTORY_SEPARATOR.'testxml1-add.xml')
        );
    }

    public function testGetXmlEncodingWithUtf8BomWithoutXmlDeclaration()
    {
        $this->assertNull(
            Utility::getXmlEncoding($this->fixtures.DIRECTORY_SEPARATOR.'testxml2-add-bom.xml')
        );
    }

    public function testGetXmlEncodingWithoutUtf8BomWithXmlDeclaration()
    {
        $this->assertSame(
            'UTF-8',
            Utility::getXmlEncoding($this->fixtures.DIRECTORY_SEPARATOR.'testxml3-add-declaration.xml')
        );
    }

    public function testGetXmlEncodingWithUtf8BomWithXmlDeclaration()
    {
        $this->assertSame(
            'UTF-8',
            Utility::getXmlEncoding($this->fixtures.DIRECTORY_SEPARATOR.'testxml4-add-bom-declaration.xml')
        );
    }

    public function testGetXmlEncodingNonUtf8()
    {
        $this->assertSame(
            'ISO-8859-1',
            Utility::getXmlEncoding($this->fixtures.DIRECTORY_SEPARATOR.'testxml5-add-iso-8859-1.xml')
        );
    }
}
