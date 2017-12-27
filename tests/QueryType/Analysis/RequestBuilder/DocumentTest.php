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

namespace Solarium\Tests\QueryType\Analysis\RequestBuilder;

use Solarium\QueryType\Analysis\Query\Document;
use Solarium\QueryType\Analysis\RequestBuilder\Document as DocumentBuilder;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Update\Query\Document\Document as InputDocument;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Document
     */
    protected $query;

    /**
     * @var DocumentBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new Document();
        $this->builder = new DocumentBuilder();
    }

    public function testBuild()
    {
        $request = $this->builder->build($this->query);

        $this->assertEquals(Request::METHOD_POST, $request->getMethod());
        $this->assertEquals($this->builder->getRawData($this->query), $request->getRawData());
    }

    public function testGetRawData()
    {
        // this doc tests data escaping
        $doc1 = new InputDocument(array('id' => 1, 'name' => 'doc1', 'cat' => 'my > cat'));

        // this doc tests a multivalue field
        $doc2 = new InputDocument(array('id' => 2, 'name' => 'doc2', 'cat' => array(1, 2, 3)));

        $this->query->addDocuments(array($doc1, $doc2));

        $this->assertEquals(
            '<docs>'.
            '<doc>'.
            '<field name="id">1</field>'.
            '<field name="name">doc1</field>'.
            '<field name="cat">my &gt; cat</field>'.
            '</doc>'.
            '<doc>'.
            '<field name="id">2</field>'.
            '<field name="name">doc2</field>'.
            '<field name="cat">1</field>'.
            '<field name="cat">2</field>'.
            '<field name="cat">3</field>'.
            '</doc>'.
            '</docs>',
            $this->builder->getRawData($this->query)
        );
    }
}
