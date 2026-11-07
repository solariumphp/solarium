<?php

namespace Solarium\Tests\QueryType\Server\Metrics;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Server\Metrics\Query;
use Solarium\QueryType\Server\Metrics\Result;

class ResultTest extends TestCase
{
    protected string $responseBody = <<<'METRICSET'
        # HELP test_result Metrics result test schema.
        # TYPE test_result info
        test_result 42
        METRICSET;

    protected Result $result;

    public function setUp(): void
    {
        $this->result = new Result(
            new Query(),
            new Response($this->responseBody, ['HTTP/1.0 200 OK'])
        );
    }

    public function testGetData(): void
    {
        $this->assertSame(
            ['metrics' => $this->responseBody],
            $this->result->getData()
        );
    }

    public function testGetMetricSet(): void
    {
        $this->assertSame(
            42,
            $this->result->getMetricSet()->getMetrics()['test_result']->metrics[0]->value
        );
    }
}
