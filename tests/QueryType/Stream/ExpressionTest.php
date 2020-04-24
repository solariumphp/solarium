<?php

namespace Solarium\Tests\QueryType\Stream;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Stream\ExpressionBuilder;

class ExpressionTest extends TestCase
{
    /**
     * @var ExpressionBuilder
     */
    protected $exp;

    public function setUp(): void
    {
        $this->exp = new ExpressionBuilder();
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

    public function testEmptyArgument()
    {
        $expression_string = $this->exp
            ->search('collection', 'q=field1:"value1"', '', 'fl="field1, field2"', 'sort="field1 ASC, field2 ASC"', 'qt="/export"');

        $this->assertSame(
            'search(collection, q=field1:"value1", fl="field1, field2", sort="field1 ASC, field2 ASC", qt="/export")',
            $expression_string
        );
    }

    public function testObject()
    {
        $expression_string = $this->exp
            ->search(new CollectionDummy(), 'q=field1:"value1"', 'fl="field1, field2"', 'sort="field1 ASC, field2 ASC"', 'qt="/export"');

        $this->assertSame(
            'search(dummy, q=field1:"value1", fl="field1, field2", sort="field1 ASC, field2 ASC", qt="/export")',
            $expression_string
        );

        $exception = null;
        try {
            $this->exp->search(new \stdClass(), 'q=field1:"value1"', 'fl="field1, field2"', 'sort="field1 ASC, field2 ASC"', 'qt="/export"');
        } catch (InvalidArgumentException $exception) {
        }

        $this->assertNotNull($exception);
    }

    public function testArray()
    {
        $exception = null;
        try {
            $this->exp->search(['array'], 'q=field1:"value1"', 'fl="field1, field2"', 'sort="field1 ASC, field2 ASC"', 'qt="/export"');
        } catch (InvalidArgumentException $exception) {
        }

        $this->assertNotNull($exception);
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

class CollectionDummy
{
    public function __toString()
    {
        return 'dummy';
    }
}
