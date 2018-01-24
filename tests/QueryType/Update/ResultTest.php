<?php

namespace Solarium\Tests\QueryType\Update;

use Solarium\QueryType\Update\Result as UpdateResult;

class ResultTest extends AbstractResultTest
{
    public function setUp()
    {
        $this->result = new UpdateDummy();
    }
}

class UpdateDummy extends UpdateResult
{
    protected $parsed = true;

    public function __construct()
    {
        $this->status = 1;
        $this->queryTime = 12;
    }
}
