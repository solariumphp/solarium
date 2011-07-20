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

class Solarium_Query_Select_FilterQueryTest extends PHPUnit_Framework_TestCase
{

    protected $_filterQuery;

    public function setUp()
    {
        $this->_filterQuery = new Solarium_Query_Select_FilterQuery;
    }

    public function testConfigMode()
    {
        $fq = new Solarium_Query_Select_FilterQuery(array('tag' => array('t1','t2'),'key' => 'k1','query'=> 'id:[10 TO 20]'));

        $this->assertEquals(array('t1','t2'), $fq->getTags());
        $this->assertEquals('k1', $fq->getKey());
        $this->assertEquals('id:[10 TO 20]', $fq->getQuery());
    }

    public function testConfigModeWithSingleValueTag()
    {
        $fq = new Solarium_Query_Select_FilterQuery(array('tag' => 't1','key' => 'k1','query'=> 'id:[10 TO 20]'));

        $this->assertEquals(array('t1'), $fq->getTags());
        $this->assertEquals('k1', $fq->getKey());
        $this->assertEquals('id:[10 TO 20]', $fq->getQuery());
    }

    public function testSetAndGetKey()
    {
        $this->_filterQuery->setKey('testkey');
        $this->assertEquals('testkey', $this->_filterQuery->getKey());
    }

    public function testSetAndGetQuery()
    {
        $this->_filterQuery->setQuery('category:1');
        $this->assertEquals('category:1', $this->_filterQuery->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->_filterQuery->setQuery('id:%1%', array(678));
        $this->assertEquals('id:678', $this->_filterQuery->getQuery());
    }

    public function testAddTag()
    {
        $this->_filterQuery->addTag('testtag');
        $this->assertEquals(array('testtag'), $this->_filterQuery->getTags());
    }

    public function testAddTags()
    {
        $this->_filterQuery->addTags(array('t1','t2'));
        $this->assertEquals(array('t1','t2'), $this->_filterQuery->getTags());
    }

    public function testRemoveTag()
    {
        $this->_filterQuery->addTags(array('t1','t2'));
        $this->_filterQuery->removeTag('t1');
        $this->assertEquals(array('t2'), $this->_filterQuery->getTags());
    }

    public function testClearTags()
    {
        $this->_filterQuery->addTags(array('t1','t2'));
        $this->_filterQuery->clearTags();
        $this->assertEquals(array(), $this->_filterQuery->getTags());
    }

    public function testSetTags()
    {
        $this->_filterQuery->addTags(array('t1','t2'));
        $this->_filterQuery->setTags(array('t3','t4'));
        $this->assertEquals(array('t3','t4'), $this->_filterQuery->getTags());
    }

}
