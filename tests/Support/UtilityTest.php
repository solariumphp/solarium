<?php

namespace Solarium\Tests\Support;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\UnexpectedValueException;
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

    /**
     * @testWith ["*", true]
     *           ["a_*", true]
     *           ["*_a", true]
     *           ["a", false]
     *           ["*_a_*", false]
     *           ["a_*_a", false]
     *           ["a_**", false]
     *           ["**_a", false]
     */
    public function testIsWildcardPattern(string $fieldName, bool $expected)
    {
        $this->assertSame($expected, Utility::isWildcardPattern($fieldName));
    }

    /**
     * @testWith ["*", "field", true]
     *           ["a_*", "a_field", true]
     *           ["a_*", "a_", true]
     *           ["*_a", "field_a", true]
     *           ["*_a", "_a", true]
     *           ["a_*", "b_field", false]
     *           ["*_a", "field_b", false]
     */
    public function testFieldMatchesWildcard(string $wildcardPattern, string $fieldName, bool $expected)
    {
        $this->assertSame($expected, Utility::fieldMatchesWildcard($wildcardPattern, $fieldName));
    }

    /**
     * @testWith ["a"]
     *           ["*_a_*"]
     *           ["a_*_a"]
     *           ["a_**"]
     *           ["**_a"]
     */
    public function testFieldMatchesWildcardInvalidWildcardPattern(string $wildcardPattern)
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Wildcard pattern must have a "*" only at the start or the end.');
        Utility::fieldMatchesWildcard($wildcardPattern, 'field');
    }

    /**
     * @testWith ["org.example.ClassName", "o.e.ClassName"]
     *           ["org.Example.ClassName", "o.E.ClassName"]
     *           ["org.example.p.ClassName", "o.e.p.ClassName"]
     *           ["org.example.v1.ClassName", "o.e.v.ClassName"]
     *           ["org.example.ClassName$InnerClassName", "o.e.ClassName$InnerClassName"]
     *           ["org.example.ClassName$1", "o.e.ClassName$1"]
     *           ["ClassName", "ClassName"]
     *           ["ClassName$InnerClassName", "ClassName$InnerClassName"]
     *           ["ClassName$1", "ClassName$1"]
     *           ["", ""]
     */
    public function testCompactSolrClassName(string $className, string $expected)
    {
        $this->assertSame($expected, Utility::compactSolrClassName($className));
    }
}
