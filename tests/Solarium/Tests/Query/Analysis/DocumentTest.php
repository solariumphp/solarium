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

namespace Solarium\Tests\Query\Analysis;

class DocumentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium\Query\Analysis\Document
     */
    protected $_query;

    public function setUp()
    {
        $this->_query = new \Solarium\Query\Analysis\Document;
    }

    public function testGetType()
    {
        $this->assertEquals(\Solarium\Client\Client::QUERYTYPE_ANALYSIS_DOCUMENT, $this->_query->getType());
    }

    public function testAddAndGetDocument()
    {
        $doc = new \Solarium\Document\ReadWrite(array('id' => 1));
        $this->_query->addDocument($doc);
        $this->assertEquals(
            array($doc),
            $this->_query->getDocuments()
        );
    }

    public function testAddAndGetDocuments()
    {
        $doc1 = new \Solarium\Document\ReadWrite(array('id' => 1));
        $doc2 = new \Solarium\Document\ReadWrite(array('id' => 2));
        $this->_query->addDocuments(array($doc1, $doc2));
        $this->assertEquals(
            array($doc1, $doc2),
            $this->_query->getDocuments()
        );
    }

}