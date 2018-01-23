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

namespace Solarium\Tests\Component\Result\Spellcheck;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Result\Spellcheck\Collation;

class CollationTest extends TestCase
{
    /**
     * @var Collation
     */
    protected $result;

    protected $corrections;
    protected $hits;
    protected $query;

    public function setUp()
    {
        $this->corrections = array(
            'key1' => 'content1',
            'key2' => 'content2',
        );
        $this->hits = 1;
        $this->query = 'dummy query';

        $this->result = new Collation($this->query, $this->hits, $this->corrections);
    }

    public function testGetQuery()
    {
        $this->assertSame($this->query, $this->result->getQuery());
    }

    public function testGetHits()
    {
        $this->assertSame($this->hits, $this->result->getHits());
    }

    public function testGetCorrections()
    {
         $this->assertSame($this->corrections, $this->result->getCorrections());
    }

    public function testIterator()
    {
        $items = array();
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertSame($this->corrections, $items);
    }

    public function testCount()
    {
        $this->assertSame(count($this->corrections), count($this->result));
    }
}
