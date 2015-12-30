<?php

namespace Solarium\Tests\QueryType\Schema\Query\FieldType\Analyzer;

use Solarium\QueryType\Schema\Query\FieldType\Analyzer\IndexAnalyzer;

class IndexAnalyzerTest extends StandardAnalyzerTest
{
    protected function setUp()
    {
        $this->analyzer = new IndexAnalyzer();
    }

    public function testGetType()
    {
        $this->assertEquals('indexAnalyzer', $this->analyzer->getType());
    }
}
