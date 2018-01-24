<?php

namespace Solarium\Tests\QueryType\Select\Result\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Facet\Query;

class QueryTest extends TestCase
{
    public function testGetValue()
    {
        $facet = new Query(124);

        $this->assertSame(124, $facet->getValue());
    }
}
