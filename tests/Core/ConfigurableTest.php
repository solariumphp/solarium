<?php

namespace Solarium\Tests\Core;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Configurable;
use Solarium\Exception\RuntimeException;

class ConfigurableTest extends TestCase
{
    public function testConstructorNoConfig()
    {
        $configTest = new ConfigTest();
        $defaultOptions = [
            'option1' => 1,
            'option2' => 'value 2',
        ];

        $this->assertSame($configTest->getOptions(), $defaultOptions);
    }

    public function testConstructorWithConfig()
    {
        $configTest = new ConfigTest(
            ['option2' => 'newvalue2', 'option3' => 3]
        );

        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = [
            'option1' => 1,
            'option2' => 'newvalue2',
            'option3' => 3,
        ];

        $this->assertSame($expectedOptions, $configTest->getOptions());
    }

    public function testGetOption()
    {
        $configTest = new ConfigTest();
        $this->assertSame(1, $configTest->getOption('option1'));
    }

    public function testGetOptionWithInvalidName()
    {
        $configTest = new ConfigTest();
        $this->assertNull($configTest->getOption('invalidoptionname'));
    }

    public function testInitialisation()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('test init');
        new ConfigTestInit();
    }

    public function testSetOptions()
    {
        $configTest = new ConfigTest();
        $configTest->setOptions(['option2' => 2, 'option3' => 3]);

        $this->assertSame(
            ['option1' => 1, 'option2' => 2, 'option3' => 3],
            $configTest->getOptions()
        );
    }

    public function testSetOptionsReturnsSelf()
    {
        $configTest = new ConfigTest();
        $this->assertSame($configTest, $configTest->setOptions([]));
    }

    public function testSetOptionsWithOverride()
    {
        $configTest = new ConfigTest();
        $configTest->setOptions(['option2' => 2, 'option3' => 3], true);

        $this->assertSame(
            ['option2' => 2, 'option3' => 3],
            $configTest->getOptions()
        );
    }

    public function testSetOptionsCallsInitLocalParameters()
    {
        $configTest = new ConfigTestInitLocalParameters();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('test initLocalParameters');
        $configTest->setOptions([]);
    }

    public function testSetOption()
    {
        $configTest = new ConfigTest();
        $configTest->setOption('option2', 'newvalue');

        $this->assertSame('newvalue', $configTest->getOption('option2'));
    }

    public function testSetOptionReturnsSelf()
    {
        $configTest = new ConfigTest();
        $this->assertSame($configTest, $configTest->setOption('option', 'value'));
    }
}

class ConfigTest extends Configurable
{
    protected $options = [
        'option1' => 1,
        'option2' => 'value 2',
    ];

    /**
     * Override visibility.
     */
    public function setOption(string $name, mixed $value): self
    {
        return parent::setOption($name, $value);
    }
}

class ConfigTestInit extends ConfigTest
{
    protected function init()
    {
        throw new RuntimeException('test init');
    }
}

class ConfigTestInitLocalParameters extends ConfigTest
{
    protected function initLocalParameters()
    {
        throw new RuntimeException('test initLocalParameters');
    }
}
