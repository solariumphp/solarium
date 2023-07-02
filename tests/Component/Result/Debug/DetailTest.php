<?php

namespace Solarium\Tests\Component\Result\Debug;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Debug\Detail;

class DetailTest extends TestCase
{
    /**
     * @var Detail
     */
    protected $result;

    protected $value;

    protected $match;

    protected $description;

    public function setUp(): void
    {
        $this->value = 1.5;
        $this->match = true;
        $this->description = 'dummy-desc';

        $this->result = new Detail(
            $this->match,
            $this->value,
            $this->description
        );
    }

    public function testGetValue()
    {
        $this->assertEquals($this->value, $this->result->getValue());
    }

    public function testGetMatch()
    {
        $this->assertEquals($this->match, $this->result->getMatch());
    }

    public function testGetDescription()
    {
        $this->assertEquals($this->description, $this->result->getDescription());
    }

    public function testSetSubDetails()
    {
        $subDetailsDummyArrays = [[
            'match' => false,
            'value' => 3.14,
            'description' => 'test',
        ]];

        $subDetailsDummyObjects = [
            new Detail(false, 3.14, 'test'),
        ];

        $this->result->setSubDetails($subDetailsDummyArrays);
        $subDetail = $this->result->getSubDetails()[0];
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
            $subDetail['match'],
            $subDetail['value'],
            $subDetail['description'],
        ]);
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
            $subDetail->getMatch(),
            $subDetail->getValue(),
            $subDetail->getDescription(),
        ]);

        $this->result->setSubDetails($subDetailsDummyObjects);
        $subDetail = $this->result->getSubDetails()[0];
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
          $subDetail['match'],
          $subDetail['value'],
          $subDetail['description'],
        ]);
        $this->assertEquals(array_values($subDetailsDummyArrays[0]), [
            $subDetail->getMatch(),
            $subDetail->getValue(),
            $subDetail->getDescription(),
        ]);
    }

    /**
     * @testWith ["match"]
     *           ["value"]
     *           ["description"]
     */
    public function testOffsetExists(string $offset)
    {
        $this->assertTrue($this->result->offsetExists($offset));
    }

    public function testOffsetExistsUnknown()
    {
        $this->assertFalse($this->result->offsetExists('unknown'));
    }

    /**
     * @testWith ["match"]
     *           ["value"]
     *           ["description"]
     */
    public function testOffsetGet(string $offset)
    {
        $this->assertSame($this->{$offset}, $this->result->offsetGet($offset));
    }

    public function testOffsetGetUnknown()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('Undefined property');
        $this->result->offsetGet('unknown');

        restore_error_handler();
    }

    public function testOffsetSetImmutable()
    {
        $this->result->offsetSet('value', 3.0);
        $this->assertSame($this->value, $this->result->getValue());
    }

    public function testOffsetUnsetImmutable()
    {
        $this->result->offsetUnset('value');
        $this->assertSame($this->value, $this->result->getValue());
    }

    public function testToString()
    {
        $expected = '1.500000 <= dummy-desc'.PHP_EOL;
        $this->assertSame($expected, (string) $this->result);
    }

    public function testToStringWithSubDetails()
    {
        $subDetails = [
            new Detail(true, 3.14, 'dummy-1'),
            new Detail(false, 2.72, 'dummy-2'),
            new Detail(true, 1.41, 'dummy-3'),
        ];
        $expected = '1.500000 <= dummy-desc'.PHP_EOL.'... 3.140000 <= dummy-1'.PHP_EOL.'... 1.410000 <= dummy-3'.PHP_EOL;

        $this->result->setSubDetails($subDetails);
        $this->assertSame($expected, (string) $this->result);
    }
}
