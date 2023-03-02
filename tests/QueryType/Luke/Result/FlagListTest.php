<?php

namespace Solarium\Tests\QueryType\Luke\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Flag;
use Solarium\QueryType\Luke\Result\FlagList;

class FlagListTest extends TestCase
{
    /**
     * @var array
     */
    protected $flags;

    /**
     * @var FlagList
     */
    protected $flagList;

    public function setUp(): void
    {
        $this->flags = [
            'A' => new Flag('A', 'A Flag'),
            'O' => new Flag('O', 'Other Flag'),
        ];
        $this->flagList = new FlagList('AO-', [
            'A' => 'A Flag',
            'O' => 'Other Flag',
            'U' => 'Unused Flag',
        ]);
    }

    public function testIsIndexed()
    {
        $flags = new FlagList('I', ['I' => new Flag('I', '...')]);
        $this->assertTrue($flags->isIndexed());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isIndexed());
    }

    public function testIsTokenized()
    {
        $flags = new FlagList('T', ['T' => new Flag('T', '...')]);
        $this->assertTrue($flags->isTokenized());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isTokenized());
    }

    public function testIsStored()
    {
        $flags = new FlagList('S', ['S' => new Flag('S', '...')]);
        $this->assertTrue($flags->isStored());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isStored());
    }

    public function testIsDocValues()
    {
        $flags = new FlagList('D', ['D' => new Flag('D', '...')]);
        $this->assertTrue($flags->isDocValues());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isDocValues());
    }

    public function testIsUninvertible()
    {
        $flags = new FlagList('U', ['U' => new Flag('U', '...')]);
        $this->assertTrue($flags->isUninvertible());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isUninvertible());
    }

    public function testIsMultiValued()
    {
        $flags = new FlagList('M', ['M' => new Flag('M', '...')]);
        $this->assertTrue($flags->isMultiValued());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isMultiValued());
    }

    public function testIsTermVectors()
    {
        $flags = new FlagList('V', ['V' => new Flag('V', '...')]);
        $this->assertTrue($flags->isTermVectors());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isTermVectors());
    }

    public function testIsTermOffsets()
    {
        $flags = new FlagList('o', ['o' => new Flag('o', '...')]);
        $this->assertTrue($flags->isTermOffsets());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isTermOffsets());
    }

    public function testIsTermPositions()
    {
        $flags = new FlagList('p', ['p' => new Flag('p', '...')]);
        $this->assertTrue($flags->isTermPositions());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isTermPositions());
    }

    public function testIsTermPayloads()
    {
        $flags = new FlagList('y', ['y' => new Flag('y', '...')]);
        $this->assertTrue($flags->isTermPayloads());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isTermPayloads());
    }

    public function testIsOmitNorms()
    {
        $flags = new FlagList('O', ['O' => new Flag('O', '...')]);
        $this->assertTrue($flags->isOmitNorms());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isOmitNorms());
    }

    public function testIsOmitTermFreqAndPositions()
    {
        $flags = new FlagList('F', ['F' => new Flag('F', '...')]);
        $this->assertTrue($flags->isOmitTermFreqAndPositions());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isOmitTermFreqAndPositions());
    }

    public function testIsOmitPositions()
    {
        $flags = new FlagList('P', ['P' => new Flag('P', '...')]);
        $this->assertTrue($flags->isOmitPositions());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isOmitPositions());
    }

    public function testIsStoreOffsetsWithPositions()
    {
        $flags = new FlagList('H', ['H' => new Flag('H', '...')]);
        $this->assertTrue($flags->isStoreOffsetsWithPositions());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isStoreOffsetsWithPositions());
    }

    public function testIsLazy()
    {
        $flags = new FlagList('L', ['L' => new Flag('L', '...')]);
        $this->assertTrue($flags->isLazy());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isLazy());
    }

    public function testIsBinary()
    {
        $flags = new FlagList('B', ['B' => new Flag('B', '...')]);
        $this->assertTrue($flags->isBinary());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isBinary());
    }

    public function testIsSortMissingFirst()
    {
        $flags = new FlagList('f', ['f' => new Flag('f', '...')]);
        $this->assertTrue($flags->isSortMissingFirst());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isSortMissingFirst());
    }

    public function testIsSortMissingLast()
    {
        $flags = new FlagList('l', ['l' => new Flag('l', '...')]);
        $this->assertTrue($flags->isSortMissingLast());

        $flags = new FlagList('-', []);
        $this->assertFalse($flags->isSortMissingLast());
    }

    public function testCountable()
    {
        $this->assertCount(2, $this->flagList);
    }

    public function testIterator()
    {
        $expected = [
            0 => ['A', new Flag('A', 'A Flag')],
            1 => ['O', new Flag('O', 'Other Flag')],
        ];
        $index = 0;

        foreach ($this->flagList as $abbrevation => $flag) {
            $this->assertSame($expected[$index][0], $abbrevation);
            $this->assertEquals($expected[$index][1], $flag);
            ++$index;
        }
    }

    public function testArrayAccess()
    {
        $this->assertArrayHasKey('O', $this->flagList);
        $this->assertArrayNotHasKey('U', $this->flagList);
        $this->assertEquals(new Flag('O', 'Other Flag'), $this->flagList['O']);
        $this->assertNull($this->flagList['U']);
    }

    public function testArrayAccessImmutable()
    {
        $this->flagList['N'] = new Flag('N', 'New Flag');
        $this->assertArrayNotHasKey('N', $this->flagList);
        unset($this->flagList['A']);
        $this->assertArrayHasKey('A', $this->flagList);
    }

    public function testToString()
    {
        $this->assertSame('AO-', (string) $this->flagList);
    }
}
