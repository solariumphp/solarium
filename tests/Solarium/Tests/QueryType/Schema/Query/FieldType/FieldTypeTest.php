<?php

namespace Solarium\Tests\QueryType\Schema\Query;

use Solarium\QueryType\Schema\Query\FieldType\FieldType;

class FieldTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFieldType()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\FieldType\FieldTypeInterface',
            $this->getNewFieldType()
        );
    }

    public function testSetAndGetNameAndClass()
    {
        $name1 = 'name1';
        $class1 = 'Class1';
        $fieldType = new FieldType($name1, $class1);
        $this->assertEquals($name1, $fieldType->getName());
        $this->assertEquals($class1, $fieldType->getClass());
        $name2 = 'name2';
        $class2 = 'Class2';
        $fieldType->setName($name2);
        $fieldType->setClass($class2);
        $this->assertEquals($name2, $fieldType->getName());
        $this->assertEquals($class2, $fieldType->getClass());
    }

    public function testSetAndGetPositionIncrementGap()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->getPositionIncrementGap());
        $gap = 2;
        $fieldType->setPositionIncrementGap($gap);
        $this->assertEquals($gap, $fieldType->getPositionIncrementGap());
    }

    public function testSetAndGetDocValuesFormat()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->getDocValuesFormat());
        $format = 'format';
        $fieldType->setDocValuesFormat($format);
        $this->assertEquals($format, $fieldType->getDocValuesFormat());
    }

    public function testSetAndGetPostingsFormat()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->getPostingsFormat());
        $format = 'format';
        $fieldType->setPostingsFormat($format);
        $this->assertEquals($format, $fieldType->getPostingsFormat());
    }

    public function testSetAndIsIndexed()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isIndexed());
        $fieldType->setIndexed(true);
        $this->assertTrue($fieldType->isIndexed());
    }

    public function testSetAndIsStored()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isStored());
        $fieldType->setStored(true);
        $this->assertTrue($fieldType->isStored());
    }

    public function testSetAndIsDocValues()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isDocValues());
        $fieldType->setDocValues(true);
        $this->assertTrue($fieldType->isDocValues());
    }

    public function testSetAndIsSortMissingFirst()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isSortMissingFirst());
        $fieldType->setSortMissingFirst(true);
        $this->assertTrue($fieldType->isSortMissingFirst());
    }

    public function testSetAndIsSortMissingLast()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isSortMissingLast());
        $fieldType->setSortMissingLast(true);
        $this->assertTrue($fieldType->isSortMissingLast());
    }

    public function testSetAndIsMultiValued()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isMultiValued());
        $fieldType->setMultiValued(true);
        $this->assertTrue($fieldType->isMultiValued());
    }

    public function testSetAndIsOmitNorms()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isOmitNorms());
        $fieldType->setOmitNorms(true);
        $this->assertTrue($fieldType->isOmitNorms());
    }

    public function testSetAndIsOmitTermFreqAndPositions()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isOmitTermFreqAndPositions());
        $fieldType->setOmitTermFreqAndPositions(true);
        $this->assertTrue($fieldType->isOmitTermFreqAndPositions());
    }

    public function testSetAndIsOmitPositions()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isOmitPositions());
        $fieldType->setOmitPositions(true);
        $this->assertTrue($fieldType->isOmitPositions());
    }

    public function testSetAndIsTermVectors()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isTermVectors());
        $fieldType->setTermVectors(true);
        $this->assertTrue($fieldType->isTermVectors());
    }

    public function testSetAndIsTermPositions()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isTermPositions());
        $fieldType->setTermPositions(true);
        $this->assertTrue($fieldType->isTermPositions());
    }

    public function testSetAndIsTermOffsets()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isTermOffsets());
        $fieldType->setTermOffsets(true);
        $this->assertTrue($fieldType->isTermOffsets());
    }

    public function testSetAndIsTermPayloads()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertNull($fieldType->isTermPayloads());
        $fieldType->setTermPayloads(true);
        $this->assertTrue($fieldType->isTermPayloads());
    }

    public function testSetAddAndGetAnalyzers()
    {
        $fieldType = $this->getNewFieldType();
        $that = $this;
        $createAnalyzer = function () use ($that) {
            return $that->getMock('Solarium\QueryType\Schema\Query\FieldType\Analyzer\AnalyzerInterface');
        };
        $analyzer1 = $createAnalyzer();
        $type1 = 'type1';
        $analyzer1
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type1));
        $analyzer2 = $createAnalyzer();
        $type2 = 'type2';
        $analyzer2
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type2));
        $analyzer3 = $createAnalyzer();
        $type3 = 'type3';
        $analyzer3
            ->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type3));
        $initialAnalyzers = array($analyzer1, $analyzer2);
        $this->assertEquals(array(), $fieldType->getAnalyzers());
        $fieldType->setAnalyzers($initialAnalyzers);
        $this->assertEquals(array($type1 => $analyzer1, $type2 => $analyzer2), $fieldType->getAnalyzers());
        $fieldType->addAnalyzer($createAnalyzer());
        $finalAnalyzers = $fieldType->getAnalyzers();
        $this->assertCount(3, $finalAnalyzers);
        $this->assertContainsOnlyInstancesOf(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\AnalyzerInterface',
            $finalAnalyzers
        );
    }

    public function testCreateIndexAnalyzer()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\IndexAnalyzer',
            $this->getNewFieldType()->createAnalyzer('index')
        );
    }

    public function testCreateQueryAnalyzer()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\QueryAnalyzer',
            $this->getNewFieldType()->createAnalyzer('query')
        );
    }

    public function testCreateStandardAnalyzer()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\StandardAnalyzer',
            $this->getNewFieldType()->createAnalyzer()
        );
    }

    public function testCreateAnalyzerWithUnknownType()
    {
        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
        $this->getNewFieldType()->createAnalyzer('unknown');
    }

    public function testCastAsArray()
    {
        $fieldType = $this->getNewFieldType();
        $indexed = false;
        $omitPositions = true;
        $fieldType->setIndexed($indexed);
        $fieldType->setOmitPositions($omitPositions);
        $analyzer = $this->getMock('Solarium\QueryType\Schema\Query\FieldType\Analyzer\AnalyzerInterface');
        $analyzerItems = array('analyzerField1' => true, 'analyzerField2' => false);
        $analyzer
            ->expects($this->any())
            ->method('castAsArray')
            ->will($this->returnValue($analyzerItems));
        $fieldType->addAnalyzer($analyzer);
        $expected = array(
            'name' => 'name',
            'class' => 'class',
            'indexed' => false,
            'omitPositions' => true,
            '' => array(
                'analyzerField1' => true,
                'analyzerField2' => false,
            ),
        );
        $this->assertEquals($expected, $fieldType->castAsArray());
    }

    public function testToString()
    {
        $this->assertEquals('name', (string) $this->getNewFieldType());
    }

    public function testArrayAccess()
    {
        $fieldType = $this->getNewFieldType();
        $this->assertTrue(isset($fieldType['class']));
        $this->assertEquals('class', $fieldType['class']);
        unset($fieldType['class']);
        $this->assertNull($fieldType['class']);
        $fieldType['class'] = 'newclass';
        $this->assertTrue(isset($fieldType['class']));
        $this->assertEquals('newclass', $fieldType['class']);
    }

    /**
     * @return FieldType
     */
    private function getNewFieldType()
    {
        return new FieldType('name', 'class');
    }
}
