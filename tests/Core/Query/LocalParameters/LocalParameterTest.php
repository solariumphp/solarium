<?php

declare(strict_types=1);

namespace Solarium\Tests\Core\Query\LocalParameters;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\LocalParameters\LocalParameter;

/**
 * LocalParameterTest.
 */
class LocalParameterTest extends TestCase
{
    /**
     * @var LocalParameter
     */
    protected $parameter;

    public function setUp(): void
    {
        $this->parameter = new LocalParameter(LocalParameter::TYPE_KEY);
    }

    public function testGetType(): void
    {
        $this->assertSame(LocalParameter::TYPE_KEY, $this->parameter->getType());
    }

    public function testSetValues(): void
    {
        $this->parameter->setValues(['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $this->parameter->getValues());

        $this->parameter->setValues(['value3', 'value4']);
        $this->assertSame(['value3', 'value4'], $this->parameter->getValues());
    }

    public function testAddValue(): void
    {
        $this->parameter->addValue('value1');
        $this->assertSame(['value1'], $this->parameter->getValues());

        $this->parameter->addValue('value2');
        $this->assertSame(['value1', 'value2'], $this->parameter->getValues());
    }

    public function testAddValues(): void
    {
        $this->parameter->addValues(['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $this->parameter->getValues());

        $this->parameter->addValues(['value3', 'value4']);
        $this->assertSame(['value1', 'value2', 'value3', 'value4'], $this->parameter->getValues());
    }

    public function testRemoveValue(): void
    {
        $this->parameter->setValues(['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $this->parameter->getValues());

        $this->parameter->removeValue('value1');
        $this->assertSame(['value2'], $this->parameter->getValues());
    }

    public function testClearValues(): void
    {
        $this->parameter->setValues(['value1', 'value2']);
        $this->assertSame(['value1', 'value2'], $this->parameter->getValues());

        $this->parameter->clearValues();
        $this->assertEmpty($this->parameter->getValues());
    }

    public function testToString(): void
    {
        $this->parameter->clearValues();
        $this->assertSame('', (string) $this->parameter);

        $this->parameter->setValues(['']);
        $this->assertSame('', (string) $this->parameter);

        $this->parameter->setValues(['value1']);
        $this->assertSame('key=value1', (string) $this->parameter);

        $this->parameter->setValues(['value1', 'value2']);
        $this->assertSame('key=value1,value2', (string) $this->parameter);
    }

    public function testIsSplitSmart(): void
    {
        $this->assertTrue(LocalParameter::isSplitSmart(LocalParameter::IS_SPLIT_SMART[0]));
        $this->assertFalse(LocalParameter::isSplitSmart('other.type'));
    }
}
