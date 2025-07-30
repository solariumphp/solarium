<?php

namespace Solarium\Tests\QueryType\Update;

use Solarium\QueryType\Update\Result as UpdateResult;

class ResultTest extends AbstractResultTestCase
{
    public function setUp(): void
    {
        $this->result = new UpdateDummy();
    }
}

class UpdateDummy extends UpdateResult
{
    protected $parsed = true;

    public function __construct()
    {
        $this->responseHeader = ['status' => 1, 'QTime' => 12];
    }
}
