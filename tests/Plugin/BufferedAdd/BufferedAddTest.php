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

namespace Solarium\Tests\Plugin\BufferedAdd;

use Solarium\QueryType\Update\Query\Document\Document;
use Solarium\Plugin\BufferedAdd\Event\AddDocument;
use Solarium\Plugin\BufferedAdd\BufferedAdd;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Endpoint;
use Solarium\Plugin\BufferedAdd\Event\Events;

class BufferedAddTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BufferedAdd
     */
    protected $plugin;

    public function setUp()
    {
        $this->plugin = new BufferedAdd();
        $this->plugin->initPlugin(new Client(), array());
    }

    public function testSetAndGetBufferSize()
    {
        $this->plugin->setBufferSize(500);
        $this->assertEquals(500, $this->plugin->getBufferSize());
    }

    public function testAddDocument()
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);

        $this->assertEquals(array($doc), $this->plugin->getDocuments());
    }

    public function testCreateDocument()
    {
        $data = array('id' => '123', 'name' => 'test');
        $doc = new Document($data);

        $this->plugin->createDocument($data);

        $this->assertEquals(array($doc), $this->plugin->getDocuments());
    }

    public function testAddDocuments()
    {
        $doc1 = new Document();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Document();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $docs = array($doc1, $doc2);

        $this->plugin->addDocuments($docs);

        $this->assertEquals($docs, $this->plugin->getDocuments());
    }

    public function testAddDocumentAutoFlush()
    {
        $mockUpdate = $this->getMock('Solarium\QueryType\Update\Query\Query', array('addDocuments'));
        $mockUpdate->expects($this->exactly(2))->method('addDocuments');

        $mockClient = $this->getMock('Solarium\Core\Client\Client', array('createUpdate', 'update', 'triggerEvent'));
        $mockClient->expects($this->exactly(3))->method('createUpdate')->will($this->returnValue($mockUpdate));
        $mockClient->expects($this->exactly(2))->method('update')->will($this->returnValue('dummyResult'));

        $doc1 = new Document();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Document();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $docs = array($doc1, $doc2);

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, array());
        $plugin->setBufferSize(1);
        $plugin->addDocuments($docs);
    }

    public function testClear()
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);
        $this->plugin->clear();

        $this->assertEquals(array(), $this->plugin->getDocuments());
    }

    public function testFlushEmptyBuffer()
    {
        $this->assertEquals(false, $this->plugin->flush());
    }

    public function testFlush()
    {
        $data = array('id' => '123', 'name' => 'test');
        $doc = new Document($data);

        $mockUpdate = $this->getMock('Solarium\QueryType\Update\Query\Query', array('addDocuments'));
        $mockUpdate->expects($this->once())
            ->method('addDocuments')
            ->with($this->equalTo(array($doc)), $this->equalTo(true), $this->equalTo(12));

        $mockClient = $this->getMock('Solarium\Core\Client\Client', array('createUpdate', 'update', 'triggerEvent'));
        $mockClient->expects($this->exactly(2))->method('createUpdate')->will($this->returnValue($mockUpdate));
        $mockClient->expects($this->once())->method('update')->will($this->returnValue('dummyResult'));

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, array());
        $plugin->addDocument($doc);

        $this->assertEquals('dummyResult', $plugin->flush(true, 12));
    }

    public function testCommit()
    {
        $data = array('id' => '123', 'name' => 'test');
        $doc = new Document($data);

        $mockUpdate = $this->getMock('Solarium\QueryType\Update\Query\Query', array('addDocuments', 'addCommit'));
        $mockUpdate->expects($this->once())
            ->method('addDocuments')
            ->with($this->equalTo(array($doc)), $this->equalTo(true));
        $mockUpdate->expects($this->once())
            ->method('addCommit')
            ->with($this->equalTo(false), $this->equalTo(true), $this->equalTo(false));

        $mockClient = $this->getMock('Solarium\Core\Client\Client', array('createUpdate', 'update', 'triggerEvent'));
        $mockClient->expects($this->exactly(2))->method('createUpdate')->will($this->returnValue($mockUpdate));
        $mockClient->expects($this->once())->method('update')->will($this->returnValue('dummyResult'));

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, array());
        $plugin->addDocument($doc);

        $this->assertEquals('dummyResult', $plugin->commit(true, false, true, false));
    }

    public function testAddDocumentEventIsTriggered()
    {
        $data = array('id' => '123', 'name' => 'test');
        $doc = new Document($data);

        $expectedEvent = new AddDocument($doc);

        $mockEventDispatcher = $this->getMock('Solarium\QueryType\Update\Query\Query', array('dispatch'));
        $mockEventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::ADD_DOCUMENT), $this->equalTo($expectedEvent));

        $mockClient = $this->getMock('Solarium\Core\Client\Client', array('getEventDispatcher'));
        $mockClient->expects($this->once())
            ->method('getEventDispatcher')
            ->will($this->returnValue($mockEventDispatcher));

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, array());
        $plugin->addDocument($doc);
    }

    public function testSetAndGetEndpoint()
    {
        $endpoint = new Endpoint();
        $endpoint->setKey('master');
        $this->assertEquals($this->plugin, $this->plugin->setEndpoint($endpoint));
        $this->assertEquals($endpoint, $this->plugin->getEndPoint());
    }
}
