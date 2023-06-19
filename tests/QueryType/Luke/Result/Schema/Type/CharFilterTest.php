<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Type\CharFilter;

class CharFilterTest extends AbstractFilterTestCase
{
    /**
     * @var CharFilter
     */
    protected $filter;

    public function setUp(): void
    {
        $this->filter = new CharFilter('CharFilter');
    }

    public function testGetName()
    {
        $this->assertSame('CharFilter', $this->filter->getName());
    }

    public function testToString()
    {
        $this->assertSame('CharFilter', (string) $this->filter);
    }
}
