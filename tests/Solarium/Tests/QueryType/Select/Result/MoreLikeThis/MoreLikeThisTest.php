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

namespace Solarium\Tests\QueryType\Select\Result\MoreLikeThis;

use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\MoreLikeThis\Result;
use Solarium\QueryType\Select\Result\MoreLikeThis\MoreLikeThis;

class MoreLikeThisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MoreLikeThis
     */
    protected $mlt;

    protected $results;

    public function setUp()
    {
        $docs = array(
            new Document(array('id'=>1, 'name'=>'test1')),
            new Document(array('id'=>2, 'name'=>'test2')),
        );

        $this->results = array(
            'key1' => new Result(2, 5.13, $docs),
            'key2' => new Result(2, 2.3, $docs),
        );

        $this->mlt = new MoreLikeThis($this->results);
    }

    public function testGetResults()
    {
         $this->assertEquals($this->results, $this->mlt->getResults());
    }

    public function testGetResult()
    {
        $this->assertEquals(
            $this->results['key1'],
            $this->mlt->getResult('key1')
        );
    }

    public function testGetInvalidResult()
    {
        $this->assertEquals(
            null,
            $this->mlt->getResult('invalid')
        );
    }

    public function testIterator()
    {
        $items = array();
        foreach ($this->mlt as $key => $item) {
            $items[$key] = $item;
        }

        $this->assertEquals($this->results, $items);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->results), count($this->mlt));
    }
}
