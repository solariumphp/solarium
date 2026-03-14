<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Result\Result;

class ResultTest extends TestCase
{
    protected CoreAdminDummyResult $result;

    public function setUp(): void
    {
        $this->result = new CoreAdminDummyResult();
    }

    public function testGetWasSuccessful(): void
    {
        $this->assertTrue($this->result->getWasSuccessful());
    }

    public function testGetStatusMessage(): void
    {
        $this->assertSame('OK', $this->result->getStatusMessage());
    }

    /**
     * @see Solarium\QueryType\Server\CoreAdmin\ResponseParser::parse()
     * @see Solarium\QueryType\Server\CoreAdmin\Result\Result::__get()
     */
    public function testAccessResponseAsProperty(): void
    {
        $data = [
            '_original_response' => [
                'timing' => [
                    'time' => 318.0,
                    'doSplit' => [
                        'time' => 318.0,
                    ],
                    'findDocSetsPerLeaf' => [
                        'time' => 0.0,
                    ],
                    'addIndexes' => [
                        'time' => 21.0,
                    ],
                    'subIWCommit' => [
                        'time' => 294.0,
                    ],
                ],
            ],
        ];

        $this->result->mapData($data);
        $this->assertSame($data['_original_response'], $this->result->response);
    }

    public function testAccessOtherProperty(): void
    {
        $this->result->mapData(['foo' => 'bar']);

        $this->assertSame('bar', $this->result->foo);
    }
}

class CoreAdminDummyResult extends Result
{
    protected bool $parsed = true;

    public function __construct()
    {
        $this->wasSuccessful = true;
        $this->statusMessage = 'OK';
    }

    public function mapData(array $mapData): void
    {
        parent::mapData($mapData);
    }
}
