<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\Plugin\Loadbalancer;

use Solarium\Plugin\Loadbalancer\WeightedRandomChoice;

class WeightedRandomChoiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRandom()
    {
        $choices = array('key1' => 1, 'key2' => 2, 'key3' => 3);

        $randomizer = new WeightedRandomChoice($choices);
        $choice = $randomizer->getRandom();

        $this->assertTrue(
            array_key_exists($choice, $choices)
        );

        $counts = array('key1' => 0, 'key2' => 0, 'key3' => 0);
        for ($i = 0; $i<1000; $i++) {
            $choice = $randomizer->getRandom();
            $counts[$choice]++;
        }

        $this->assertTrue($counts['key1'] < $counts['key2']);
        $this->assertTrue($counts['key2'] < $counts['key3']);
    }

    public function testGetRandomWithExclude()
    {
        $choices = array('key1' => 1, 'key2' => 1, 'key3' => 300);
        $excludes = array('key3');

        $randomizer = new WeightedRandomChoice($choices);

        $key = $randomizer->getRandom($excludes);

        $this->assertTrue($key !== 'key3');
    }

    public function testAllEntriesExcluded()
    {
        $choices = array('key1' => 1, 'key2' => 2, 'key3' => 3);
        $excludes = array_keys($choices);

        $randomizer = new WeightedRandomChoice($choices);

        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $randomizer->getRandom($excludes);
    }

    public function testInvalidWeigth()
    {
        $choices = array('key1' => -1, 'key2' => 2);
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        new WeightedRandomChoice($choices);
    }
}
