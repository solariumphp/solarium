<?php

namespace Solarium\Tests\QueryType\Stream\Query;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Stream\Expression;

class ExpressionTest extends TestCase
{
    /**
     * @var Expression
     */
    protected $exp;

    public function setUp()
    {
        $this->exp = new Expression();
    }

    public function testExpression()
    {
        $expression_string = $this->exp
            ->search('collection', 'q=field1:"value1"', 'fq="field2:value2"', 'fl="field1, field2"', 'sort="field1 ASC, field2 ASC"', 'qt="/export"');

        $this->assertSame(
            'search(collection, q=field1:"value1", fq="field2:value2", fl="field1, field2", sort="field1 ASC, field2 ASC", qt="/export")',
            $expression_string
        );
    }

    public function testNestedExpressions()
    {
        $expression_string =
            $this->exp->innerJoin(
                $this->exp->search('collection', 'q=field1:"value1"', 'fq="field2:value2"', 'fl="field1, field2"', 'sort="field1 ASC, field2 ASC"', 'qt="/export"'),
                $this->exp->search('collection', 'q=field3:"value3"', 'fl="field3, field4"', 'sort="field4 ASC"', 'qt="/export"'),
                'on="field1=field2"'
            );

        $this->assertSame(
            'innerJoin(search(collection, q=field1:"value1", fq="field2:value2", fl="field1, field2", sort="field1 ASC, field2 ASC", qt="/export"), search(collection, q=field3:"value3", fl="field3, field4", sort="field4 ASC", qt="/export"), on="field1=field2")',
            $expression_string
        );
    }
}
