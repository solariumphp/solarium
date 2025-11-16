<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Type\AbstractFilter;
use Solarium\QueryType\Luke\Result\Schema\Type\CharFilter;

class CharFilterTest extends AbstractFilterTestCase
{
    protected AbstractFilter|CharFilter $filter;

    public function setUp(): void
    {
        $this->filter = new CharFilter('CharFilter');
    }

    public function testGetName(): void
    {
        $this->assertSame('CharFilter', $this->filter->getName());
    }

    public function testToString(): void
    {
        $this->assertSame('CharFilter', (string) $this->filter);
    }
}
