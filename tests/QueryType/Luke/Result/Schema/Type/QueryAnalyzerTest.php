<?php

namespace Solarium\Tests\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Type\QueryAnalyzer;

class QueryAnalyzerTest extends AbstractAnalyzerTestCase
{
    /**
     * @var QueryAnalyzer
     */
    protected $analyzer;

    public function setUp(): void
    {
        $this->analyzer = new QueryAnalyzer('org.example.QueryAnalyzerClass');
    }

    public function testGetClassName()
    {
        $this->assertSame('org.example.QueryAnalyzerClass', $this->analyzer->getClassName());
    }

    public function testToString()
    {
        $this->assertSame('org.example.QueryAnalyzerClass', (string) $this->analyzer);
    }
}
