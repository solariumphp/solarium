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

use Solarium\Core\Client\Client;
use Solarium\QueryType\Update\Query\Document\Document;
use Solarium\QueryType\Extract\Query;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = new Query;
    }

    public function testGetType()
    {
        $this->assertEquals(Client::QUERY_EXTRACT, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Update\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Extract\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testConfigMode()
    {
        $mappings = array(
            'from1' => 'to1',
            'from2' => 'to2',
        );

        $options = array(
            'fmap' => $mappings,
        );

        $this->query->setOptions($options);

        $this->assertEquals(
            $mappings,
            $this->query->getFieldMappings()
        );
    }

    public function testSetAndGetStart()
    {
        $doc = new Document(array('field1', 'value1'));
        $this->query->setDocument($doc);
        $this->assertEquals($doc, $this->query->getDocument());
    }

    public function testSetAndGetFilename()
    {
        $this->query->setFile(__FILE__);
        $this->assertEquals(__FILE__, $this->query->getFile());
    }

    public function testSetAndGetUprefix()
    {
        $this->query->setUprefix('dyn_');
        $this->assertEquals('dyn_', $this->query->getUprefix());
    }

    public function testSetAndGetDefaultField()
    {
        $this->query->setDefaultField('defaulttext');
        $this->assertEquals('defaulttext', $this->query->getDefaultField());
    }

    public function testSetAndGetExtractOnly()
    {
        $this->query->setExtractOnly(true);
        $this->assertEquals(true, $this->query->getExtractOnly());
    }

    public function testSetAndGetLowernames()
    {
        $this->query->setLowernames(true);
        $this->assertEquals(true, $this->query->getLowernames());
    }

    public function testSetAndGetCommit()
    {
        $this->query->setCommit(true);
        $this->assertEquals(true, $this->query->getCommit());
    }

    public function testSetAndGetCommitWithin()
    {
        $this->query->setCommitWithin(458);
        $this->assertEquals(458, $this->query->getCommitWithin());
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('Solarium\Tests\QueryType\Extract\MyCustomDoc');
        $this->assertEquals('Solarium\Tests\QueryType\Extract\MyCustomDoc', $this->query->getDocumentClass());

        return $this->query;
    }

    /**
     * @depends testSetAndGetDocumentClass
     */
    public function testCreateDocument($query)
    {
        $fields = array('key1' => 'value1', 'key2' => 'value2');
        $boosts = array('key1' => 1, 'key2' => 2);
        $document = $query->createDocument($fields, $boosts);

        $this->assertInstanceOf('Solarium\Tests\QueryType\Extract\MyCustomDoc', $document);
        $this->assertEquals($boosts, $document->getFieldBoosts());
        $this->assertEquals($fields, $document->getFields());
    }

    public function testAddFieldMapping()
    {
        $expectedFields = $this->query->getFieldMappings();
        $expectedFields['newfield'] = 'tofield';
        $this->query->addFieldMapping('newfield', 'tofield');
        $this->assertEquals($expectedFields, $this->query->getFieldMappings());

        return $this->query;
    }

    /**
     * @depends testAddFieldMapping
     * @param Query $query
     */
    public function testClearFieldMappingss($query)
    {
        $query->clearFieldMappings();
        $this->assertEquals(array(), $query->getFieldMappings());

        return $query;
    }

    /**
     * @depends testClearFieldMappingss
     * @param Query $query
     */
    public function testAddFieldMappings($query)
    {
        $fields = array('field1' => 'target1', 'field2' => 'target2');
        $query->addFieldMappings($fields);
        $this->assertEquals($fields, $query->getFieldMappings());

        return $query;
    }

    /**
     * @depends testAddFieldMappings
     * @param Query $query
     */
    public function testRemoveFieldMapping($query)
    {
        $query->removeFieldMapping('field1');
        $this->assertEquals(array('field2' => 'target2'), $query->getFieldMappings());

        return $query;
    }

    /**
     * @depends testRemoveFieldMapping
     * @param Query $query
     */
    public function testSetFields($query)
    {
        $fields = array('field3' => 'target3', 'field4' => 'target4');
        $query->setFieldMappings($fields);
        $this->assertEquals($fields, $query->getFieldMappings());
    }
}

class MyCustomDoc extends Document
{
}
