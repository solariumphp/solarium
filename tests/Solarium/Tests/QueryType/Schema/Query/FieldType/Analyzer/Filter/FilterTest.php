<?php

namespace Solarium\Tests\QueryType\Schema\Query\FieldType\Analyzer\Filter;

use Solarium\QueryType\Schema\Query\FieldType\Analyzer\Filter\Filter;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filter
     */
    private $filter;

    protected function setUp()
    {
        $this->class = 'this-is-a-class';
        $this->attributes = array(
            'this' => 'that',
        );
        $this->filter = new Filter($this->class, $this->attributes);
    }

    public function testIsFilter()
    {
        $this->assertInstanceOf('Solarium\QueryType\Schema\Query\FieldType\Analyzer\Filter\FilterInterface', $this->filter);
    }

    public function testSetAndGetClass()
    {
        $this->assertEquals($this->class, $this->filter->getClass());
        $class2 = 'class2';
        $this->filter->setClass($class2);
        $this->assertEquals($class2, $this->filter->getClass());
    }

    public function testSetGetAndAddAttributes()
    {
        $this->assertEquals($this->attributes, $this->filter->getAttributes());
        $this->filter->setAttributes(array('key1' => 'value1'));
        $this->filter->addAttributes(array('key2' => 'value2'));
        $this->filter->addAttribute('key3', 'value3');
        $this->filter['key4'] = 'value4';
        $this->assertEquals(
            array(
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
                'key4' => 'value4',
            ),
            $this->filter->getAttributes()
        );
        $this->assertEquals('value3', $this->filter['key3']);
    }

    public function testCastAsArray()
    {
        $this->assertEquals(
            array(
                'class' => $this->class,
                'this' => 'that',
            ),
            $this->filter->castAsArray()
        );
    }
}
