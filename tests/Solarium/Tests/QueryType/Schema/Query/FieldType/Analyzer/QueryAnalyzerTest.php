<?php

namespace Solarium\Tests\QueryType\Schema\Query\FieldType\Analyzer;

use Solarium\QueryType\Schema\Query\FieldType\Analyzer\QueryAnalyzer;

class QueryAnalyzerTest extends StandardAnalyzerTest
{
    protected function setUp()
    {
        $this->analyzer = new QueryAnalyzer();
    }

    public function testGetType()
    {
        $this->assertEquals('queryAnalyzer', $this->analyzer->getType());
    }
}
