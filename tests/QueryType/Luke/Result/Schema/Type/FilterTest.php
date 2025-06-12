<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Type\Filter;

class FilterTest extends AbstractFilterTestCase
{
    /**
     * @var Filter
     */
    protected $filter;

    public function setUp(): void
    {
        $this->filter = new Filter('Filter');
    }

    public function testGetName(): void
    {
        $this->assertSame('Filter', $this->filter->getName());
    }

    public function testToString(): void
    {
        $this->assertSame('Filter', (string) $this->filter);
    }
}
