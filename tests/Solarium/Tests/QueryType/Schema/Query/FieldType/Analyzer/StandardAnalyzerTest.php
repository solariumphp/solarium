<?php

namespace Solarium\Tests\QueryType\Schema\Query\FieldType\Analyzer;

use Solarium\QueryType\Schema\Query\FieldType\Analyzer\StandardAnalyzer;

class StandardAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StandardAnalyzer
     */
    protected $analyzer;

    protected function setUp()
    {
        $this->analyzer = new StandardAnalyzer();
    }

    public function testIsAnalyzer()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\AnalyzerInterface',
            $this->analyzer
        );
    }

    public function testGetType()
    {
        $this->assertEquals('analyzer', $this->analyzer->getType());
    }

    public function testSetAndGetClass()
    {
        $this->assertNull($this->analyzer->getClass());
        $class2 = 'class2';
        $this->analyzer->setClass($class2);
        $this->assertEquals($class2, $this->analyzer->getClass());
    }

    public function testSetGetAndCreateTokenizer()
    {
        $this->assertNull($this->analyzer->getTokenizer());
        $tokenizer = $this->getMock(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer\TokenizerInterface'
        );
        $this->analyzer->setTokenizer($tokenizer);
        $this->assertSame($tokenizer, $this->analyzer->getTokenizer());
        $tokenizer2 = $this->analyzer->createTokenizer('class2', ',');
        $this->assertInstanceOf(
            'Solarium\QueryType\Schema\Query\FieldType\Analyzer\Tokenizer\TokenizerInterface',
            $tokenizer2
        );
        $this->assertSame($tokenizer2, $this->analyzer->getTokenizer());
    }

    public function testAccessFilters()
    {
        $this->assertEquals(array(), $this->analyzer->getFilters());
        $filter1 = $this->getMockFilter();
        $this->analyzer->setFilters(array($filter1));
        $filter2 = $this->getMockFilter();
        $this->analyzer->addFilter($filter2);
        $this->assertEquals(array($filter1, $filter2), $this->analyzer->getFilters());
        $filter3 = $this->analyzer->createFilter('class3', array('key3' => 'value3'));
        $this->assertInstanceOf($this->getFilterInterfaceFqcn(), $filter3);
        $this->assertCount(3, $this->analyzer->getFilters());
    }

    public function testCastAsArray()
    {
        $class = 'class3';
        $this->analyzer->setClass($class);
        $this->analyzer->createTokenizer('class2', ';');
        $this->analyzer->createFilter('class4', array('this' => 'that'));
        $this->assertEquals(
            array(
                'class' => $class,
                'tokenizer' => array(
                    'class' => 'class2',
                    'delimiter' => ';',
                ),
                'filters' => array(
                    array(
                        'class' => 'class4',
                        'this' => 'that',
                    ),
                ),
            ),
            $this->analyzer->castAsArray()
        );
    }

    protected function getMockFilter()
    {
        return $this->getMock($this->getFilterInterfaceFqcn());
    }

    protected function getFilterInterfaceFqcn()
    {
        return 'Solarium\QueryType\Schema\Query\FieldType\Analyzer\Filter\FilterInterface';
    }
}
