<?php

namespace Solarium\Tests\Core;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Configurable;
use Solarium\Exception\InvalidArgumentException;
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

    public function testConstructorWithObject()
    {
        $configTest = new ConfigTest(new MyConfigObject());

        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = [
            'option1' => 1,
            'option2' => 'newvalue2',
            'option3' => 3,
        ];

        $this->assertSame($expectedOptions, $configTest->getOptions());
    }

    public function testConstructorWithObjectWithoutToArray()
    {
        $config = new \stdClass();
        $config->option2 = 'newvalue2';
        $config->option3 = 3;
        $config->option4 = [
            'option5' => 5,
            'option6' => 'newvalue6',
        ];

        $configTest = new ConfigTest($config);

        // the default options should be merged with the constructor values,
        // overwriting any default values.
        $expectedOptions = [
            'option1' => 1,
            'option2' => 'newvalue2',
            'option3' => 3,
            'option4' => [
                'option5' => 5,
                'option6' => 'newvalue6',
            ],
        ];

        $this->assertSame($expectedOptions, $configTest->getOptions());
    }

    public function testConstructorWithArrayConfig()
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

    public function testGetOptionWIthInvalidName()
    {
        $configTest = new ConfigTest();
        $this->assertNull($configTest->getOption('invalidoptionname'));
    }

    public function testInitialisation()
    {
        $this->expectException(RuntimeException::class);
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

    public function testSetOptionsWithOverride()
    {
        $configTest = new ConfigTest();
        $configTest->setOptions(['option2' => 2, 'option3' => 3], true);

        $this->assertSame(
            ['option2' => 2, 'option3' => 3],
            $configTest->getOptions()
        );
    }

    public function testSetOptionsWithInvalidArgument()
    {
        $configTest = new ConfigTest();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Options value given to the setOptions() method must be an array or a Zend_Config object');
        $configTest->setOptions('option2=2&option3=3');
    }
}

class ConfigTest extends Configurable
{
    protected $options = [
        'option1' => 1,
        'option2' => 'value 2',
    ];
}

class ConfigTestInit extends ConfigTest
{
    protected function init()
    {
        throw new RuntimeException('test init');
    }
}

class MyConfigObject
{
    public function toArray()
    {
        return ['option2' => 'newvalue2', 'option3' => 3];
    }
}
