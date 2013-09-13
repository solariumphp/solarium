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

namespace Solarium\Tests\Plugin;

use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;
use Solarium\Core\Client\Client;
use Solarium\Plugin\PrefetchIterator;

class PrefetchIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrefetchIterator
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->plugin = new PrefetchIterator();

        $this->client = new Client();
        $this->query = $this->client->createSelect();

    }

    public function testSetAndGetPrefetch()
    {
        $this->plugin->setPrefetch(120);
        $this->assertEquals(120, $this->plugin->getPrefetch());
    }

    public function testSetAndGetQuery()
    {
        $this->plugin->setQuery($this->query);
        $this->assertEquals($this->query, $this->plugin->getQuery());
    }

    public function testCount()
    {
        $result = $this->getResult();
        $mockClient = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $mockClient->expects($this->exactly(1))->method('execute')->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, array());
        $this->plugin->setQuery($this->query);
        $this->assertEquals(5, count($this->plugin));
    }

    public function testIteratorAndRewind()
    {
        $result = $this->getResult();
        $mockClient = $this->getMock('Solarium\Core\Client\Client', array('execute'));
        $mockClient->expects($this->exactly(1))->method('execute')->will($this->returnValue($result));

        $this->plugin->initPlugin($mockClient, array());
        $this->plugin->setQuery($this->query);

        $results1 = array();
        foreach ($this->plugin as $doc) {
            $results1[] = $doc;
        }

        // the second foreach will trigger a rewind, this time include keys
        $results2 = array();
        foreach ($this->plugin as $key => $doc) {
            $results2[$key] = $doc;
        }

        $this->assertEquals($result->getDocuments(), $results1);
        $this->assertEquals($result->getDocuments(), $results2);
    }

    public function getResult()
    {
        $numFound = 5;

        $docs = array(
            new Document(array('id'=>1, 'title'=>'doc1')),
            new Document(array('id'=>2, 'title'=>'doc2')),
            new Document(array('id'=>3, 'title'=>'doc3')),
            new Document(array('id'=>4, 'title'=>'doc4')),
            new Document(array('id'=>5, 'title'=>'doc5')),
        );

        return new SelectDummy(1, 12, $numFound, $docs, array());
    }
}

class SelectDummy extends Result
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $numfound, $docs, $components)
    {
        $this->numfound = $numfound;
        $this->documents = $docs;
        $this->components = $components;
        $this->queryTime = $queryTime;
        $this->status = $status;
    }
}
