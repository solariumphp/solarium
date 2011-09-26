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

class Solarium_Query_Select_Component_FieldTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Highlighting_Field
     */
    protected $_fld;

    public function setUp()
    {
        $this->_fld = new Solarium_Query_Select_Component_Highlighting_Field;
    }

    public function testConfigMode()
    {
        $options = array(
            'snippets' => 3,
            'fragsize' => 25,
            'mergecontiguous' => true,
            'alternatefield' => 'text',
            'formatter' => 'myFormatter',
            'simpleprefix' => '<b>',
            'simplepostfix' => '</b>',
            'fragmenter' => 'myFragmenter',
            'usefastvectorhighlighter' => true,
        );

        $this->_fld->setOptions($options);

        $this->assertEquals(3, $this->_fld->getSnippets());
        $this->assertEquals(25, $this->_fld->getFragSize());
        $this->assertEquals(true, $this->_fld->getMergeContiguous());
        $this->assertEquals('text', $this->_fld->getAlternateField());
        $this->assertEquals('myFormatter', $this->_fld->getFormatter());
        $this->assertEquals('<b>', $this->_fld->getSimplePrefix());
        $this->assertEquals('</b>', $this->_fld->getSimplePostfix());
        $this->assertEquals('myFragmenter', $this->_fld->getFragmenter());
        $this->assertEquals(true, $this->_fld->getUseFastVectorHighlighter());
    }

    public function testSetAndGetName()
    {
        $value = 'testname';
        $this->_fld->setName($value);

        $this->assertEquals(
            $value,
            $this->_fld->getName()
        );
    }

    public function testSetAndGetSnippets()
    {
        $value = 2;
        $this->_fld->setSnippets($value);

        $this->assertEquals(
            $value,
            $this->_fld->getSnippets()
        );
    }

    public function testSetAndGetFragSize()
    {
        $value = 20;
        $this->_fld->setFragsize($value);

        $this->assertEquals(
            $value,
            $this->_fld->getFragSize()
        );
    }

    public function testSetAndGetMergeContiguous()
    {
        $value = true;
        $this->_fld->setMergeContiguous($value);

        $this->assertEquals(
            $value,
            $this->_fld->getMergeContiguous()
        );
    }

    public function testSetAndGetAlternateField()
    {
        $value = 'description';
        $this->_fld->setAlternateField($value);

        $this->assertEquals(
            $value,
            $this->_fld->getAlternateField()
        );
    }

    public function testSetAndGetFormatter()
    {
        $this->_fld->setFormatter();

        $this->assertEquals(
            'simple',
            $this->_fld->getFormatter()
        );
    }

    public function testSetAndGetSimplePrefix()
    {
        $value = '<em>';
        $this->_fld->setSimplePrefix($value);

        $this->assertEquals(
            $value,
            $this->_fld->getSimplePrefix()
        );
    }

    public function testSetAndGetSimplePostfix()
    {
        $value = '</em>';
        $this->_fld->setSimplePostfix($value);

        $this->assertEquals(
            $value,
            $this->_fld->getSimplePostfix()
        );
    }

    public function testSetAndGetFragmenter()
    {
        $value = Solarium_Query_Select_Component_Highlighting::FRAGMENTER_REGEX;
        $this->_fld->setFragmenter($value);

        $this->assertEquals(
            $value,
            $this->_fld->getFragmenter()
        );
    }

    public function testSetAndGetUseFastVectorHighlighter()
    {
        $value = true;
        $this->_fld->setUseFastVectorHighlighter($value);

        $this->assertEquals(
            $value,
            $this->_fld->getUseFastVectorHighlighter()
        );
    }

}
