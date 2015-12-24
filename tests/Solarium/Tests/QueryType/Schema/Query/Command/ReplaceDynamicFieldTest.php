<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\ReplaceDynamicField;
use Solarium\QueryType\Schema\Query\Query;

class ReplaceDynamicFieldTest extends ReplaceFieldTest
{
    protected function setUp()
    {
        $this->command = new ReplaceDynamicField();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_REPLACE_DYNAMIC_FIELD, $this->command->getType());
    }
}
