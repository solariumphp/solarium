<?php

namespace Solarium\Tests\Plugin\CustomizeRequest;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\CustomizeRequest\Customization;

class CustomizationTest extends TestCase
{
    protected Customization $instance;

    public function setUp(): void
    {
        $this->instance = new Customization();
    }

    public function testSetAndGetKey(): void
    {
        $value = 'mykey';
        $this->instance->setKey($value);
        $this->assertSame($value, $this->instance->getKey());
    }

    public function testSetAndGetName(): void
    {
        $value = 'myname';
        $this->instance->setName($value);
        $this->assertSame($value, $this->instance->getName());
    }

    public function testSetAndGetType(): void
    {
        $value = 'mytype';
        $this->instance->setType($value);
        $this->assertSame($value, $this->instance->getType());
    }

    public function testSetAndGetValue(): void
    {
        $value = 'myvalue';
        $this->instance->setValue($value);
        $this->assertSame($value, $this->instance->getValue());
    }

    public function testSetAndGetPersistence(): void
    {
        $this->instance->setPersistent(true);
        $this->assertTrue($this->instance->getPersistent());
    }

    public function testSetAndGetOverwrite(): void
    {
        $this->instance->setOverwrite(false);
        $this->assertFalse($this->instance->getOverwrite());
    }

    public function testIsValid(): void
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('param');
        $this->instance->setName('myname');
        $this->instance->setValue('myvalue');
        $this->assertTrue($this->instance->isValid());
    }

    public function testIsValidWithInvalidType(): void
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('mytype');
        $this->instance->setName('myname');
        $this->instance->setValue('myvalue');

        $this->assertFalse($this->instance->isValid());
    }

    public function testIsValidWithMissingValue(): void
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('param');
        $this->instance->setName('myname');

        $this->assertFalse($this->instance->isValid());
    }
}
