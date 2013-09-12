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

namespace Solarium\Tests\QueryType\Select\Result\Debug;

use Solarium\QueryType\Select\Result\Debug\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Document
     */
    protected $result;

    protected $value;
    protected $match;
    protected $description;
    protected $key;
    protected $details;

    public function setUp()
    {
        $this->key = 'dummy-key';
        $this->value = 1.5;
        $this->match = true;
        $this->description = 'dummy-desc';
        $this->details = array(0 => 'dummy1', 1 => 'dummy2');

        $this->result = new Document(
            $this->key,
            $this->match,
            $this->value,
            $this->description,
            $this->details
        );
    }

    public function testGetKey()
    {
         $this->assertEquals($this->key, $this->result->getKey());
    }

    public function testGetDetails()
    {
         $this->assertEquals($this->details, $this->result->getDetails());
    }

    public function testIterator()
    {
        $items = array();
        foreach ($this->result as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->details, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->details), count($this->result));
    }
}
