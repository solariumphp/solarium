<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Schema\Type\AbstractFilter;

abstract class AbstractFilterTestCase extends TestCase
{
    /**
     * @var AbstractFilter
     */
    protected $filter;

    abstract public function testGetName();

    public function testSetAndGetArgs()
    {
        $args = [
            'class' => 'solr.FilterFactory',
            'luceneMatchVersion' => '1.2.3',
        ];
        $this->assertSame($this->filter, $this->filter->setArgs($args));
        $this->assertSame($args, $this->filter->getArgs());
    }

    public function testSetAndGetClassName()
    {
        $this->assertSame($this->filter, $this->filter->setClassName('org.example.FilterFactory'));
        $this->assertSame('org.example.FilterFactory', $this->filter->getClassName());
    }

    abstract public function testToString();
}
