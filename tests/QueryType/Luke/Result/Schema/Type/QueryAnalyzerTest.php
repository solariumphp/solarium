<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Type\AbstractAnalyzer;
use Solarium\QueryType\Luke\Result\Schema\Type\QueryAnalyzer;

class QueryAnalyzerTest extends AbstractAnalyzerTestCase
{
    protected AbstractAnalyzer|QueryAnalyzer $analyzer;

    public function setUp(): void
    {
        $this->analyzer = new QueryAnalyzer('org.example.QueryAnalyzerClass');
    }

    public function testGetClassName(): void
    {
        $this->assertSame('org.example.QueryAnalyzerClass', $this->analyzer->getClassName());
    }

    public function testToString(): void
    {
        $this->assertSame('org.example.QueryAnalyzerClass', (string) $this->analyzer);
    }
}
