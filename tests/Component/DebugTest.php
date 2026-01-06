<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Debug;
use Solarium\Component\RequestBuilder\Debug as DebugBuilder;
use Solarium\Component\ResponseParser\Debug as DebugParser;

class DebugTest extends TestCase
{
    protected Debug $debug;

    public function setUp(): void
    {
        $this->debug = new Debug();
    }

    public function testConfigMode(): void
    {
        $options = [
            'explainother' => 'id:12',
        ];

        $this->debug->setOptions($options);

        $this->assertEquals($options['explainother'], $this->debug->getExplainOther());
    }

    public function testGetType(): void
    {
        $this->assertEquals(
            ComponentAwareQueryInterface::COMPONENT_DEBUG,
            $this->debug->getType()
        );
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(DebugParser::class, $this->debug->getResponseParser());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(DebugBuilder::class, $this->debug->getRequestBuilder());
    }

    public function testSetAndGetExplainOther(): void
    {
        $value = 'id:24';
        $this->debug->setExplainOther($value);

        $this->assertEquals(
            $value,
            $this->debug->getExplainOther()
        );
    }
}
