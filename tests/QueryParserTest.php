<?php

declare(strict_types=1);

namespace Solarium\Core\Query\QueryParser;

use PHPUnit\Framework\TestCase;

class QueryParserTest extends TestCase
{
    public function testCollapse(): void
    {
        $collapse = new CollapseQueryParser();

        self::assertIsString((string) $collapse);
    }
}
