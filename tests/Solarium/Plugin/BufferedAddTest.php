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

class Solarium_Plugin_BufferedAddTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Solarium_Plugin_BufferedAdd
     */
    protected $_plugin;

    public function setUp()
    {
        $this->_plugin = new Solarium_Plugin_BufferedAdd();
        $this->_plugin->init(new Solarium_Client(), array());
    }

    public function testSetAndGetBufferSize()
    {
        $this->_plugin->setBufferSize(500);
        $this->assertEquals(500, $this->_plugin->getBufferSize());
    }

    public function testAddDocument()
    {
        $doc = new Solarium_Document_ReadWrite();
        $doc->id = '123';
        $doc->name = 'test';

        $this->_plugin->addDocument($doc);

        $this->assertEquals(array($doc), $this->_plugin->getDocuments());
    }

    public function testCreateDocument()
    {
        $data = array('id' => '123', 'name' => 'test');
        $doc = new Solarium_Document_ReadWrite($data);

        $this->_plugin->createDocument($data);

        $this->assertEquals(array($doc), $this->_plugin->getDocuments());
    }

    public function testAddDocuments()
    {
        $doc1 = new Solarium_Document_ReadWrite();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Solarium_Document_ReadWrite();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $docs = array($doc1, $doc2);

        $this->_plugin->addDocuments($docs);

        $this->assertEquals($docs, $this->_plugin->getDocuments());
    }

    public function testAddDocumentAutoFlush()
    {
        $observer = $this->getMock('Solarium_Plugin_BufferedAdd', array('flush'));
        $observer->expects($this->once())->method('flush');
        $observer->setBufferSize(1);

        $doc1 = new Solarium_Document_ReadWrite();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Solarium_Document_ReadWrite();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $docs = array($doc1, $doc2);

        $observer->addDocuments($docs);
    }

    public function testClear()
    {
        $doc = new Solarium_Document_ReadWrite();
        $doc->id = '123';
        $doc->name = 'test';

        $this->_plugin->addDocument($doc);
        $this->_plugin->clear();

        $this->assertEquals(array(), $this->_plugin->getDocuments());
    }

    public function testFlushEmptyBuffer()
    {
        $this->assertEquals(false, $this->_plugin->flush());
    }

    public function testFlush()
    {
        $data = array('id' => '123', 'name' => 'test');
        $doc = new Solarium_Document_ReadWrite($data);

        $mockUpdate = $this->getMock('Solarium_Query_Update', array('addDocuments'));
        $mockUpdate->expects($this->once())->method('addDocuments')->with($this->equalTo(array($doc)),$this->equalTo(true),$this->equalTo(12));

        $mockClient = $this->getMock('Solarium_Client', array('createUpdate', 'update', 'triggerEvent'));
        $mockClient->expects($this->exactly(2))->method('createUpdate')->will($this->returnValue($mockUpdate));
        $mockClient->expects($this->once())->method('update')->will($this->returnValue('dummyResult'));
        $mockClient->expects($this->exactly(2))->method('triggerEvent');

        $plugin = new Solarium_Plugin_BufferedAdd();
        $plugin->init($mockClient, array());
        $plugin->addDocument($doc);

        $this->assertEquals('dummyResult', $plugin->flush(true,12));
    }

    public function testCommit()
    {
        $data = array('id' => '123', 'name' => 'test');
        $doc = new Solarium_Document_ReadWrite($data);

        $mockUpdate = $this->getMock('Solarium_Query_Update', array('addDocuments', 'addCommit'));
        $mockUpdate->expects($this->once())->method('addDocuments')->with($this->equalTo(array($doc)),$this->equalTo(true));
        $mockUpdate->expects($this->once())->method('addCommit')->with($this->equalTo(false),$this->equalTo(true),$this->equalTo(false));

        $mockClient = $this->getMock('Solarium_Client', array('createUpdate', 'update', 'triggerEvent'));
        $mockClient->expects($this->exactly(2))->method('createUpdate')->will($this->returnValue($mockUpdate));
        $mockClient->expects($this->once())->method('update')->will($this->returnValue('dummyResult'));
        $mockClient->expects($this->exactly(2))->method('triggerEvent');

        $plugin = new Solarium_Plugin_BufferedAdd();
        $plugin->init($mockClient, array());
        $plugin->addDocument($doc);

        $this->assertEquals('dummyResult', $plugin->commit(true, false, true, false));
    }

}