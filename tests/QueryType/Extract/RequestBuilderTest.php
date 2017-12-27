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

namespace Solarium\Tests\QueryType\Extract;

use Solarium\QueryType\Extract\Query;
use Solarium\QueryType\Extract\RequestBuilder;
use Solarium\Core\Client\Request;

class RequestBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp()
    {
        $this->query = new Query;
        $this->query->setFile(__FILE__);
        $this->query->addParam('param1', 'value1');
        $this->query->addFieldMapping('from-field', 'to-field');
        $this->builder = new RequestBuilder;
    }

    public function testGetMethod()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            Request::METHOD_POST,
            $request->getMethod()
        );
    }

    public function testGetFileUpload()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            __FILE__,
            $request->getFileUpload()
        );
    }

    public function testGetUri()
    {
        $request = $this->builder->build($this->query);
        $this->assertEquals(
            'update/extract?omitHeader=true&param1=value1&wt=json&json.nl=flat&extractOnly=false&fmap.from-field=to-field'.
            '&resource.name=RequestBuilderTest.php',
            $request->getUri()
        );
    }

    public function testGetUriWithStreamUrl()
    {
        $query = $this->query;
        $query->setFile('http://solarium-project.org/');
        $request = $this->builder->build($query);
        $this->assertEquals(
            'update/extract?omitHeader=true&param1=value1&wt=json&json.nl=flat&extractOnly=false&fmap.from-field=to-field'.
            '&stream.url=http%3A%2F%2Fsolarium-project.org%2F',
            $request->getUri()
        );
    }

    public function testDocumentFieldAndBoostParams()
    {
        $fields = array('field1' => 'value1', 'field2' => 'value2');
        $boosts = array('field1' => 1, 'field2' => 5);
        $document = $this->query->createDocument($fields, $boosts);
        $this->query->setDocument($document);

        $request = $this->builder->build($this->query);
        $this->assertEquals(
            array(
                'boost.field1' => 1,
                'boost.field2' => 5,
                'fmap.from-field' => 'to-field',
                'literal.field1' => 'value1',
                'literal.field2' => 'value2',
                'omitHeader' => 'true',
                'extractOnly' => 'false',
                'param1' => 'value1',
                'resource.name' => 'RequestBuilderTest.php',
                'wt' => 'json',
                'json.nl' => 'flat',
            ),
            $request->getParams()
        );
    }

    public function testDocumentWithBoostThrowsException()
    {
        $document = $this->query->createDocument();
        $document->setBoost(4);
        $this->query->setDocument($document);

        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $this->builder->build($this->query);
    }

    public function testContentTypeHeader()
    {
        $headers = array(
            'Content-Type: multipart/form-data'
        );
        $request = $this->builder->build($this->query);
        $this->assertEquals($headers,
                            $request->getHeaders());
    }
}
