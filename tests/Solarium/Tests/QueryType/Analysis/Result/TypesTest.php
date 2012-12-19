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

namespace Solarium\Tests\QueryType\Analysis\Result;
use Solarium\QueryType\Analysis\Result\Types;

class TypesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Types
     */
    protected $result;

    protected $items, $name;

    public function setUp()
    {
        $this->name = 'testname';
        $this->items = array(
            'index' => new testAnalysisTypeIndexDummy(),
            'query' => new testAnalysisTypeQueryDummy()
        );
        $this->result = new Types($this->name, $this->items);
    }

    public function testGetItems()
    {
        $this->assertEquals($this->items, $this->result->getItems());
    }

    public function testCount()
    {
        $this->assertEquals(count($this->items), count($this->result));
    }

    public function testIterator()
    {
        $lists = array();
        foreach ($this->result AS $key => $list) {
            $lists[$key] = $list;
        }

        $this->assertEquals($this->items, $lists);
    }

    public function testGetName()
    {
        $this->assertEquals(
            $this->name,
            $this->result->getName()
        );
    }

    public function testGetIndexAnalysis()
    {
        $this->assertEquals(
            $this->items['index'],
            $this->result->getIndexAnalysis()
        );
    }

    public function testGetIndexAnalysisNoData()
    {
        $items = array(
            'index' => new testAnalysisTypeInvalidDummy(),
            'query' => new testAnalysisTypeQueryDummy()
        );

        $result = new Types($this->name, $items);
        $this->assertEquals(
            null,
            $result->getIndexAnalysis()
        );
    }

    public function testGetQueryAnalysis()
    {
        $this->assertEquals(
            $this->items['query'],
            $this->result->getQueryAnalysis()
        );
    }

    public function testGetQueryAnalysisNoData()
    {
        $items = array(
            'index' => new testAnalysisTypeIndexDummy(),
            'query' => new testAnalysisTypeInvalidDummy()
        );

        $result = new Types($this->name, $items);
        $this->assertEquals(
            null,
            $result->getQueryAnalysis()
        );
    }

}

class testAnalysisTypeIndexDummy
{
    public function getName()
    {
        return 'index';
    }
}

class testAnalysisTypeQueryDummy
{
    public function getName()
    {
        return 'query';
    }
}

class testAnalysisTypeInvalidDummy
{
    public function getName()
    {
        return 'invalid';
    }
}
