<?php

namespace Solarium\Tests\QueryType\Extract;

use Solarium\QueryType\Extract\Result as ExtractResult;
use Solarium\Tests\QueryType\Update\AbstractResultTest;

class ResultTest extends AbstractResultTest
{
    public function setUp()
    {
        $this->result = new ExtractResultDummy();
    }
}

class ExtractResultDummy extends ExtractResult
{
    protected $parsed = true;

    public function __construct()
    {
        $this->status = 1;
        $this->queryTime = 12;
    }
}
