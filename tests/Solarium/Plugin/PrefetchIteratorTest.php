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

class Solarium_Plugin_PrefetchIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Solarium_Plugin_PrefetchIterator
     */
    protected $_plugin;

    /**
     * @var Solarium_Client
     */
    protected $_client;

    /**
     * @var Solarium_Query_Select
     */
    protected $_query;


    public function setUp()
    {
        $this->_plugin = new Solarium_Plugin_PrefetchIterator();

        $this->_client = new Solarium_Client();
        $this->_query = $this->_client->createSelect();

    }

    public function testSetAndGetPrefetch()
    {
        $this->_plugin->setPrefetch(120);
        $this->assertEquals(120, $this->_plugin->getPrefetch());
    }

    public function testSetAndGetQuery()
    {
        $this->_plugin->setQuery($this->_query);
        $this->assertEquals($this->_query, $this->_plugin->getQuery());
    }

    public function testCount()
    {
        $result = $this->_getResult();
        $mockClient = $this->getMock('Solarium_Client', array('execute'));
        $mockClient->expects($this->exactly(1))->method('execute')->will($this->returnValue($result));

        $this->_plugin->init($mockClient, array());
        $this->_plugin->setQuery($this->_query);
        $this->assertEquals(5, count($this->_plugin));
    }

    public function testIteratorAndRewind()
    {
        $result = $this->_getResult();
        $mockClient = $this->getMock('Solarium_Client', array('execute'));
        $mockClient->expects($this->exactly(1))->method('execute')->will($this->returnValue($result));

        $this->_plugin->init($mockClient, array());
        $this->_plugin->setQuery($this->_query);

        $results1 = array();
        foreach($this->_plugin as $doc) {
            $results1[] = $doc;
        }

        // the second foreach will trigger a rewind, this time include keys
        $results2 = array();
        foreach($this->_plugin as $key => $doc) {
            $results2[$key] = $doc;
        }

        $this->assertEquals($result->getDocuments(), $results1);
        $this->assertEquals($result->getDocuments(), $results2);
    }

    public function _getResult()
    {
        $numFound = 5;

        $docs = array(
            new Solarium_Document_ReadOnly(array('id'=>1,'title'=>'doc1')),
            new Solarium_Document_ReadOnly(array('id'=>2,'title'=>'doc2')),
            new Solarium_Document_ReadOnly(array('id'=>3,'title'=>'doc3')),
            new Solarium_Document_ReadOnly(array('id'=>4,'title'=>'doc4')),
            new Solarium_Document_ReadOnly(array('id'=>5,'title'=>'doc5')),
        );

        return new Solarium_Result_SelectDummy(1, 12, $numFound, $docs, array());
    }
}