<?php

namespace Solarium\Tests\Component\Result\TermVector;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\TermVector\Warnings;

class WarningsTest extends TestCase
{
    /**
     * @var Warnings
     */
    protected $warnings;

    public function setUp(): void
    {
        $noTermVectors = ['fieldA', 'fieldB'];
        $noPositions = ['fieldC', 'fieldD'];
        $noOffsets = ['fieldE', 'fieldF'];
        $noPayloads = ['fieldG', 'fieldH'];

        $this->warnings = new Warnings($noTermVectors, $noPositions, $noOffsets, $noPayloads);
    }

    public function testGetNoTermVectors()
    {
        $this->assertSame(['fieldA', 'fieldB'], $this->warnings->getNoTermVectors());
    }

    public function testGetNoPositions()
    {
        $this->assertSame(['fieldC', 'fieldD'], $this->warnings->getNoPositions());
    }

    public function testGetNoOffsets()
    {
        $this->assertSame(['fieldE', 'fieldF'], $this->warnings->getNoOffsets());
    }

    public function testGetNoPayloads()
    {
        $this->assertSame(['fieldG', 'fieldH'], $this->warnings->getNoPayloads());
    }

    public function testGetEmptyWarnings()
    {
        $warnings = new Warnings(null, null, null, null);

        $this->assertSame([], $warnings->getNoTermVectors());
        $this->assertSame([], $warnings->getNoPositions());
        $this->assertSame([], $warnings->getNoOffsets());
        $this->assertSame([], $warnings->getNoPayloads());
    }

    /**
     * @testWith ["noTermVectors"]
     *           ["noPositions"]
     *           ["noOffsets"]
     *           ["noPayloads"]
     */
    public function testOffsetExists(string $offset)
    {
        $this->assertTrue($this->warnings->offsetExists($offset));
    }

    public function testOffsetExistsUnknown()
    {
        $this->assertFalse($this->warnings->offsetExists('unknown'));
    }

    /**
     * @testWith ["noTermVectors", ["fieldA", "fieldB"]]
     *           ["noPositions", ["fieldC", "fieldD"]]
     *           ["noOffsets", ["fieldE", "fieldF"]]
     *           ["noPayloads", ["fieldG", "fieldH"]]
     */
    public function testOffsetGet(string $offset, array $expected)
    {
        $this->assertSame($expected, $this->warnings->offsetGet($offset));
    }

    public function testOffsetGetUnknown()
    {
        set_error_handler(static function (int $errno, string $errstr): never {
            throw new \Exception($errstr, $errno);
        }, \E_WARNING);

        $this->expectExceptionMessage('Undefined property');
        $this->warnings->offsetGet('unknown');

        restore_error_handler();
    }

    public function testOffsetSetImmutable()
    {
        $this->warnings->offsetSet('noTermVectors', ['fieldY', 'fieldZ']);
        $this->assertSame(['fieldA', 'fieldB'], $this->warnings['noTermVectors']);
    }

    public function testOffsetUnsetImmutable()
    {
        $this->warnings->offsetUnset('noTermVectors');
        $this->assertSame(['fieldA', 'fieldB'], $this->warnings['noTermVectors']);
    }
}
