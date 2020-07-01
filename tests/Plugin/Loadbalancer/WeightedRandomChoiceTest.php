<?php

namespace Solarium\Tests\Plugin\Loadbalancer;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\Loadbalancer\WeightedRandomChoice;

class WeightedRandomChoiceTest extends TestCase
{
    public function testGetRandom()
    {
        $choices = ['key1' => 1, 'key2' => 2, 'key3' => 3];

        $randomizer = new WeightedRandomChoice($choices);
        $choice = $randomizer->getRandom();

        $this->assertTrue(
            array_key_exists($choice, $choices)
        );

        $counts = ['key1' => 0, 'key2' => 0, 'key3' => 0];
        for ($i = 0; $i < 1000; ++$i) {
            $choice = $randomizer->getRandom();
            ++$counts[$choice];
        }

        $this->assertTrue($counts['key1'] < $counts['key2']);
        $this->assertTrue($counts['key2'] < $counts['key3']);
    }

    public function testGetRandomWithExclude()
    {
        $choices = ['key1' => 1, 'key2' => 1, 'key3' => 300];
        $excludes = ['key3'];

        $randomizer = new WeightedRandomChoice($choices);

        $key = $randomizer->getRandom($excludes);

        $this->assertNotSame($key, 'key3');
    }

    public function testAllEntriesExcluded()
    {
        $choices = ['key1' => 1, 'key2' => 2, 'key3' => 3];
        $excludes = array_keys($choices);

        $randomizer = new WeightedRandomChoice($choices);

        $this->expectException(RuntimeException::class);
        $randomizer->getRandom($excludes);
    }

    public function testInvalidWeigth()
    {
        $choices = ['key1' => -1, 'key2' => 2];
        $this->expectException(InvalidArgumentException::class);
        new WeightedRandomChoice($choices);
    }
}
