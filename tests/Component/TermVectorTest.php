<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\RequestBuilder\TermVector as RequestBuilder;
use Solarium\Component\ResponseParser\TermVector as ResponseParser;
use Solarium\Component\TermVector;
use Solarium\QueryType\Select\Query\Query;

class TermVectorTest extends TestCase
{
    /**
     * @var TermVector
     */
    protected $termVector;

    public function setUp(): void
    {
        $this->termVector = new TermVector();
        $this->termVector->setQueryInstance(new Query());
    }

    public function testConfigMode()
    {
        $options = [
            'docids' => '1, 2',
            'fields' => 'fieldA, fieldB',
            'all' => true,
            'documentfrequency' => true,
            'offsets' => true,
            'positions' => true,
            'payloads' => true,
            'termfrequency' => true,
            'termfreqinversedocfreq' => true,
        ];

        $this->termVector->setOptions($options);

        $this->assertSame(['1', '2'], $this->termVector->getDocIds());
        $this->assertSame(['fieldA', 'fieldB'], $this->termVector->getFields());
        $this->assertTrue($this->termVector->getAll());
        $this->assertTrue($this->termVector->getDocumentFrequency());
        $this->assertTrue($this->termVector->getOffsets());
        $this->assertTrue($this->termVector->getPositions());
        $this->assertTrue($this->termVector->getPayloads());
        $this->assertTrue($this->termVector->getTermFrequency());
        $this->assertTrue($this->termVector->getTermFreqInverseDocFreq());
    }

    public function testGetType()
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_TERMVECTOR, $this->termVector->getType());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            RequestBuilder::class,
            $this->termVector->getRequestBuilder()
        );
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            ResponseParser::class,
            $this->termVector->getResponseParser()
        );
    }

    public function testSetAndGetDocIds()
    {
        $this->termVector->setDocIds('1, 2');
        $this->assertSame(['1', '2'], $this->termVector->getDocIds());
    }

    public function testSetAndGetDocIdsWithArray()
    {
        $this->termVector->setDocIds([1, 2]);
        $this->assertSame([1, 2], $this->termVector->getDocIds());
    }

    public function testGetDocIdsAlwaysReturnsArray()
    {
        $this->termVector->setDocIds(null);
        $this->assertSame([], $this->termVector->getDocIds());
    }

    public function testSetAndGetFields()
    {
        $this->termVector->setFields('fieldA, fieldB');
        $this->assertSame(['fieldA', 'fieldB'], $this->termVector->getFields());
    }

    public function testSetAndGetFieldsWithArray()
    {
        $this->termVector->setFields(['fieldA', 'fieldB']);
        $this->assertSame(['fieldA', 'fieldB'], $this->termVector->getFields());
    }

    public function testGetFieldsAlwaysReturnsArray()
    {
        $this->termVector->setFields(null);
        $this->assertSame([], $this->termVector->getFields());
    }

    public function testSetAndGetAll()
    {
        $this->assertSame($this->termVector, $this->termVector->setAll(true));
        $this->assertTrue($this->termVector->getAll());
    }

    public function testSetAndGetDocumentFrequency()
    {
        $this->assertSame($this->termVector, $this->termVector->setDocumentFrequency(true));
        $this->assertTrue($this->termVector->getDocumentFrequency());
    }

    public function testSetAndGetOffsets()
    {
        $this->assertSame($this->termVector, $this->termVector->setOffsets(true));
        $this->assertTrue($this->termVector->getOffsets());
    }

    public function testSetAndGetPositions()
    {
        $this->assertSame($this->termVector, $this->termVector->setPositions(true));
        $this->assertTrue($this->termVector->getPositions());
    }

    public function testSetAndGetPayloads()
    {
        $this->assertSame($this->termVector, $this->termVector->setPayloads(true));
        $this->assertTrue($this->termVector->getPayloads());
    }

    public function testSetAndGetTermFrequency()
    {
        $this->assertSame($this->termVector, $this->termVector->setTermFrequency(true));
        $this->assertTrue($this->termVector->getTermFrequency());
    }

    public function testSetAndGetTermFreqInverseDocFreq()
    {
        $this->assertSame($this->termVector, $this->termVector->setTermFreqInverseDocFreq(true));
        $this->assertTrue($this->termVector->getTermFreqInverseDocFreq());
    }
}
