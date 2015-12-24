<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\ReplaceFieldType;
use Solarium\QueryType\Schema\Query\Query;

class ReplaceFieldTypeTest extends AddFieldTypeTest
{
    protected function setUp()
    {
        $this->command = new ReplaceFieldType();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_REPLACE_FIELD_TYPE, $this->command->getType());
    }
}
