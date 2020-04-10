<?php

namespace Solarium\Tests\Plugin\CustomizeRequest;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\CustomizeRequest\Customization;

class CustomizationTest extends TestCase
{
    /**
     * @var Customization
     */
    protected $instance;

    public function setUp(): void
    {
        $this->instance = new Customization();
    }

    public function testSetAndGetKey()
    {
        $value = 'mykey';
        $this->instance->setKey($value);
        $this->assertSame($value, $this->instance->getKey());
    }

    public function testSetAndGetName()
    {
        $value = 'myname';
        $this->instance->setName($value);
        $this->assertSame($value, $this->instance->getName());
    }

    public function testSetAndGetType()
    {
        $value = 'mytype';
        $this->instance->setType($value);
        $this->assertSame($value, $this->instance->getType());
    }

    public function testSetAndGetValue()
    {
        $value = 'myvalue';
        $this->instance->setValue($value);
        $this->assertSame($value, $this->instance->getValue());
    }

    public function testSetAndGetPersistence()
    {
        $this->instance->setPersistent(true);
        $this->assertTrue($this->instance->getPersistent());
    }

    public function testSetAndGetOverwrite()
    {
        $this->instance->setOverwrite(false);
        $this->assertFalse($this->instance->getOverwrite());
    }

    public function testIsValid()
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('param');
        $this->instance->setName('myname');
        $this->instance->setValue('myvalue');
        $this->assertTrue($this->instance->isValid());
    }

    public function testIsValidWithInvalidType()
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('mytype');
        $this->instance->setName('myname');
        $this->instance->setValue('myvalue');

        $this->assertFalse($this->instance->isValid());
    }

    public function testIsValidWithMissingValue()
    {
        $this->instance->setKey('mykey');
        $this->instance->setType('param');
        $this->instance->setName('myname');

        $this->assertFalse($this->instance->isValid());
    }
}
