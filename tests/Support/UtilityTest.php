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
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('No such file or directory');
        $this->assertNull(
            Utility::getXmlEncoding('nosuchfile')
        );

        restore_error_handler();
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

    public function testIsPointValue()
    {
        // geodetic, non-geodetic PointType
        $values = ['45,93', '45,-93', '-45,93', '-45,-93', '45.15,93.85', '45.15,-93.85', '-45.15,93.85', '-45.15,-93.85', '-45,93.85', '-45.15,93'];
        foreach ($values as $value) {
            $this->assertTrue(Utility::isPointValue($value));
        }

        // non-geodetic RPT
        $values = ['45 93', '45 -93', '-45 93', '-45 -93', '45.15 93.85', '45.15 -93.85', '-45.15 93.85', '-45.15 -93.85', '-45 93.85', '-45.15 93'];
        foreach ($values as $value) {
            $this->assertTrue(Utility::isPointValue($value));
        }

        $values = ['45', '-93', '45.15', '-93.85', '', 'not a point value'];
        foreach ($values as $value) {
            $this->assertFalse(Utility::isPointValue($value));
        }
    }
}
