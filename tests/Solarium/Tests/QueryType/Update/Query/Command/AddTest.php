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

namespace Solarium\Tests\QueryType\Update\Query\Command;

use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Query\Document\Document;

class AddTest extends \PHPUnit_Framework_TestCase
{
    protected $command;

    public function setUp()
    {
        $this->command = new Add;
    }

    public function testGetType()
    {
        $this->assertEquals(
            Query::COMMAND_ADD,
            $this->command->getType()
        );
    }

    public function testAddDocument()
    {
        $doc = new Document(array('id' => 1));
        $this->command->addDocument($doc);
        $this->assertEquals(
            array($doc),
            $this->command->getDocuments()
        );
    }

    public function testAddDocumentWithInvalidDocument()
    {
        // Starting from PHP7 typehints are checked by PHP and handled using a TypeException. For versions 5.x Solarium
        // needs to do this, so only test for those versions.
        if (version_compare(PHP_VERSION, '6.0.0') >= 0) {
            $this->markTestSkipped('Typehint handling check not needed, built into current PHP version');
        }

        try {
            $doc = new \StdClass();
            $this->command->addDocument($doc);

            $this->fail(
                'The addDocument() method should not accept anything else than DocumentInterface instances.'
            );
        } catch (\PHPUnit_Framework_Error $e) {
            $this->assertContains(
                'Argument 1 passed to '.get_class($this->command).'::addDocument() must implement interface '.
                'Solarium\QueryType\Update\Query\Document\DocumentInterface',
                $e->getMessage()
            );
        }
    }

    public function testAddDocuments()
    {
        $doc1 = new Document(array('id' => 1));
        $doc2 = new Document(array('id' => 2));
        $this->command->addDocuments(array($doc1, $doc2));
        $this->assertEquals(
            array($doc1, $doc2),
            $this->command->getDocuments()
        );
    }

    public function testAddDocumentsMultipleTimes()
    {
        $doc1 = new Document(array('id' => 1));
        $doc2 = new Document(array('id' => 2));
        $this->command->addDocuments(array($doc1, $doc2));

        $doc3 = new Document(array('id' => 3));
        $doc4 = new Document(array('id' => 4));
        $this->command->addDocuments(array($doc3, $doc4));

        $this->assertEquals(
            array($doc1, $doc2, $doc3, $doc4),
            $this->command->getDocuments()
        );
    }

    public function testAddDocumentsIteration()
    {
        $doc1 = new Document(array('id' => 1));
        $doc2 = new Document(array('id' => 2));

        $it = new \ArrayIterator(array($doc1, $doc2));

        $this->command->addDocuments($it);

        if ($this->command->getDocuments() instanceof \Traversable) {
            $command_documents = iterator_to_array($this->command->getDocuments());
        } else {
            $command_documents = $this->command->getDocuments();
        }

        $this->assertEquals(
            array($doc1, $doc2),
            $command_documents,
            'checking first two documents are added correctly'
        );

        $doc3 = new Document(array('id' => 3));
        $doc4 = new Document(array('id' => 4));
        $doc5 = new Document(array('id' => 5));

        $it2 = new \ArrayIterator(array($doc3, $doc4, $doc5));

        $this->command->addDocuments($it2);

        if ($this->command->getDocuments() instanceof \Traversable) {
            $command_documents = iterator_to_array($this->command->getDocuments());
        } else {
            $command_documents = $this->command->getDocuments();
        }

        $this->assertEquals(
            array($doc1, $doc2, $doc3, $doc4, $doc5),
            $command_documents,
            'checking second three documents are added correctly to first two'
        );
    }

    /**
     * @depends testAddDocumentsIteration
     */
    public function testAddDocumentToIteration()
    {
        $doc1 = new Document(array('id' => 1));
        $doc2 = new Document(array('id' => 2));

        $this->command->addDocuments(new \ArrayIterator(array($doc1, $doc2)));

        $doc3 = new Document(array('id' => 3));

        $this->command->addDocument($doc3);

        if ($this->command->getDocuments() instanceof \Traversable) {
            $command_documents = iterator_to_array($this->command->getDocuments());
        } else {
            $command_documents = $this->command->getDocuments();
        }

        $this->assertEquals(
            array($doc1, $doc2, $doc3),
            $command_documents
        );
    }

    public function testGetAndSetOverwrite()
    {
        $this->command->setOverwrite(false);
        $this->assertEquals(
            false,
            $this->command->getOverwrite()
        );
    }

    public function testGetAndSetCommitWithin()
    {
        $this->command->setCommitWithin(100);
        $this->assertEquals(
            100,
            $this->command->getCommitWithin()
        );
    }
}
