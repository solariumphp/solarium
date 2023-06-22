<?php

namespace Solarium\Tests\QueryType\Select\Query;

use Solarium\QueryType\Select\Query\Query;

class QueryTest extends AbstractQueryTestCase
{
    public function setUp(): void
    {
        $this->query = new Query();
    }
}
