<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\ReplaceField;
use Solarium\QueryType\Schema\Query\Query;

class ReplaceFieldTest extends AddFieldTest
{
    protected function setUp()
    {
        $this->command = new ReplaceField();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_REPLACE_FIELD, $this->command->getType());
    }
}
