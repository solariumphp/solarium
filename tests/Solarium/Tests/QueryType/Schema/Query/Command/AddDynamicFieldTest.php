<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\AddDynamicField;
use Solarium\QueryType\Schema\Query\Query;

class AddDynamicFieldTest extends AddFieldTest
{
    protected function setUp()
    {
        $this->command = new AddDynamicField();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_ADD_DYNAMIC_FIELD, $this->command->getType());
    }
}
