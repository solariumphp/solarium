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

namespace Solarium\Tests\QueryType\Select\Result;

use Solarium\QueryType\Select\Result\Document;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    protected $doc;

    protected $fields = array(
        'id' => 123,
        'name' => 'Test document',
        'categories' => array(1, 2, 3)
    );

    protected function setUp()
    {
        $this->doc = new Document($this->fields);
    }

    public function testGetFields()
    {
        $this->assertEquals($this->fields, $this->doc->getFields());
    }

    public function testGetFieldAsProperty()
    {
        $this->assertEquals(
            $this->fields['categories'],
            $this->doc->categories
        );
    }

    public function testGetInvalidFieldAsProperty()
    {
        $this->assertEquals(
            null,
            $this->doc->invalidfieldname
        );
    }

    public function testSetField()
    {
        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $this->doc->newField = 'new value';
    }

    public function testIterator()
    {
        $fields = array();
        foreach ($this->doc as $key => $field) {
            $fields[$key] = $field;
        }

        $this->assertEquals($this->fields, $fields);
    }

    public function testArrayGet()
    {
        $this->assertEquals(
            $this->fields['categories'],
            $this->doc['categories']
        );
    }

    public function testArrayGetInvalidField()
    {
        $this->assertEquals(
            null,
            $this->doc['invalidfield']
        );
    }

    public function testArrayIsset()
    {
        $this->assertTrue(
            isset($this->doc['categories'])
        );
    }

    public function testArrayIssetInvalidField()
    {
        $this->assertFalse(
            isset($this->doc['invalidfield'])
        );
    }

    public function testArraySet()
    {
        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $this->doc['newField'] = 'new value';
    }

    public function testArrayUnset()
    {
        $this->setExpectedException('Solarium\Exception\RuntimeException');
        unset($this->doc['newField']);
    }

    public function testCount()
    {
        $this->assertEquals(count($this->fields), count($this->doc));
    }
}
