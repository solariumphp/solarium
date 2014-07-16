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

namespace Solarium\Tests\QueryType\Select\Query\Component\Highlighting;

use Solarium\QueryType\Select\Query\Component\Highlighting\Highlighting;
use Solarium\QueryType\Select\Query\Component\Highlighting\Field;
use Solarium\QueryType\Select\Query\Query;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Field
     */
    protected $fld;

    public function setUp()
    {
        $this->fld = new Field;
    }

    public function testConfigMode()
    {
        $options = array(
            'snippets' => 3,
            'fragsize' => 25,
            'mergecontiguous' => true,
            'alternatefield' => 'text',
            'preservemulti' => true,
            'formatter' => 'myFormatter',
            'simpleprefix' => '<b>',
            'simplepostfix' => '</b>',
            'fragmenter' => 'myFragmenter',
            'usefastvectorhighlighter' => true,
        );

        $this->fld->setOptions($options);

        $this->assertEquals(3, $this->fld->getSnippets());
        $this->assertEquals(25, $this->fld->getFragSize());
        $this->assertEquals(true, $this->fld->getMergeContiguous());
        $this->assertEquals('text', $this->fld->getAlternateField());
        $this->assertEquals(true, $this->fld->getPreserveMulti());
        $this->assertEquals('myFormatter', $this->fld->getFormatter());
        $this->assertEquals('<b>', $this->fld->getSimplePrefix());
        $this->assertEquals('</b>', $this->fld->getSimplePostfix());
        $this->assertEquals('myFragmenter', $this->fld->getFragmenter());
        $this->assertEquals(true, $this->fld->getUseFastVectorHighlighter());
    }

    public function testSetAndGetName()
    {
        $value = 'testname';
        $this->fld->setName($value);

        $this->assertEquals(
            $value,
            $this->fld->getName()
        );
    }

    public function testSetAndGetSnippets()
    {
        $value = 2;
        $this->fld->setSnippets($value);

        $this->assertEquals(
            $value,
            $this->fld->getSnippets()
        );
    }

    public function testSetAndGetFragSize()
    {
        $value = 20;
        $this->fld->setFragsize($value);

        $this->assertEquals(
            $value,
            $this->fld->getFragSize()
        );
    }

    public function testSetAndGetMergeContiguous()
    {
        $value = true;
        $this->fld->setMergeContiguous($value);

        $this->assertEquals(
            $value,
            $this->fld->getMergeContiguous()
        );
    }

    public function testSetAndGetAlternateField()
    {
        $value = 'description';
        $this->fld->setAlternateField($value);

        $this->assertEquals(
            $value,
            $this->fld->getAlternateField()
        );
    }
    
    public function testSetAndGetPreserveMulti()
    {
        $value = true;
        $this->fld->setPreserveMulti($value);

        $this->assertEquals(
            $value,
            $this->fld->getPreserveMulti()
        );
    }

    public function testSetAndGetFormatter()
    {
        $this->fld->setFormatter();

        $this->assertEquals(
            'simple',
            $this->fld->getFormatter()
        );
    }

    public function testSetAndGetSimplePrefix()
    {
        $value = '<em>';
        $this->fld->setSimplePrefix($value);

        $this->assertEquals(
            $value,
            $this->fld->getSimplePrefix()
        );
    }

    public function testSetAndGetSimplePostfix()
    {
        $value = '</em>';
        $this->fld->setSimplePostfix($value);

        $this->assertEquals(
            $value,
            $this->fld->getSimplePostfix()
        );
    }

    public function testSetAndGetFragmenter()
    {
        $value = Highlighting::FRAGMENTER_REGEX;
        $this->fld->setFragmenter($value);

        $this->assertEquals(
            $value,
            $this->fld->getFragmenter()
        );
    }

    public function testSetAndGetUseFastVectorHighlighter()
    {
        $value = true;
        $this->fld->setUseFastVectorHighlighter($value);

        $this->assertEquals(
            $value,
            $this->fld->getUseFastVectorHighlighter()
        );
    }
}
