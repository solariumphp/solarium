<?php

namespace Solarium\Tests\QueryType\Schema\Query\Command;

use Solarium\QueryType\Schema\Query\Command\DeleteCopyField;
use Solarium\QueryType\Schema\Query\Query;

class DeleteCopyFieldTest extends AddCopyFieldTest
{
    protected function setUp()
    {
        $this->command = new DeleteCopyField();
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMMAND_DELETE_COPY_FIELD, $this->command->getType());
    }
}
