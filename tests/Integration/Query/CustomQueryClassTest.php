<?php

namespace Solarium\Tests\Integration\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Component\QueryInterface;

class CustomQueryClassTest extends TestCase
{
    /**
     * Test the various return types that are valid for custom query classes that
     * override the {@see \Solarium\Component\QueryTrait::setQuery()} method.
     *
     * If this test throws a fatal error, the return type of the parent might no
     * longer be backward compatible with existing code that overrides it.
     *
     * @see https://github.com/solariumphp/solarium/issues/1097
     *
     * @dataProvider customQueryClassProvider
     */
    public function testCustomQueryClassSetQueryReturnType(string $queryClass)
    {
        $query = new $queryClass();
        $this->assertInstanceOf(QueryInterface::class, $query->setQuery('*:*'));
    }

    public function customQueryClassProvider(): array
    {
        return [
            [CustomStaticQuery::class],
            [CustomSelfQuery::class],
            [CustomQueryInterfaceQuery::class],
        ];
    }
}
