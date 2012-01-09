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

class Solarium_Query_Select_Component_HighlightingTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Highlighting
     */
    protected $_hlt;

    public function setUp()
    {
        $this->_hlt = new Solarium_Query_Select_Component_Highlighting;
    }

    public function testConfigMode()
    {
        $options = array(
            'field' => array(
                'fieldA' => array(
                    'snippets' => 3,
                    'fragsize' => 25,
                ),
                'fieldB'
            ),
            'snippets' => 2,
            'fragsize' => 20,
            'mergecontiguous' => true,
            'requirefieldmatch' => false,
            'maxanalyzedchars' => 40,
            'alternatefield' => 'text',
            'maxalternatefieldlength' => 50,
            'formatter' => 'myFormatter',
            'simpleprefix' => '<b>',
            'simplepostfix' => '</b>',
            'fragmenter' => 'myFragmenter',
            'fraglistbuilder' => 'regex',
            'fragmentsbuilder' => 'myBuilder',
            'usefastvectorhighlighter' => true,
            'usephrasehighlighter' => false,
            'highlightmultiterm' => true,
            'regexslop' => .8,
            'regexpattern' => 'myPattern',
            'regexmaxanalyzedchars' => 500,
            'query' => 'text:myvalue',
            'phraselimit' => 35,

        );

        $this->_hlt->setOptions($options);

        $this->assertEquals(array('fieldA','fieldB'), array_keys($this->_hlt->getFields()));
        $this->assertEquals($options['field']['fieldA']['snippets'], $this->_hlt->getField('fieldA')->getSnippets());
        $this->assertEquals($options['field']['fieldA']['fragsize'], $this->_hlt->getField('fieldA')->getFragSize());
        $this->assertEquals(null, $this->_hlt->getField('FieldB')->getSnippets());
        $this->assertEquals($options['snippets'], $this->_hlt->getSnippets());
        $this->assertEquals($options['fragsize'], $this->_hlt->getFragSize());
        $this->assertEquals($options['mergecontiguous'], $this->_hlt->getMergeContiguous());
        $this->assertEquals($options['maxanalyzedchars'], $this->_hlt->getMaxAnalyzedChars());
        $this->assertEquals($options['alternatefield'], $this->_hlt->getAlternateField());
        $this->assertEquals($options['maxalternatefieldlength'], $this->_hlt->getMaxAlternateFieldLength());
        $this->assertEquals($options['formatter'], $this->_hlt->getFormatter());
        $this->assertEquals($options['simpleprefix'], $this->_hlt->getSimplePrefix());
        $this->assertEquals($options['simplepostfix'], $this->_hlt->getSimplePostfix());
        $this->assertEquals($options['fragmenter'], $this->_hlt->getFragmenter());
        $this->assertEquals($options['fraglistbuilder'], $this->_hlt->getFragListBuilder());
        $this->assertEquals($options['fragmentsbuilder'], $this->_hlt->getFragmentsBuilder());
        $this->assertEquals($options['usefastvectorhighlighter'], $this->_hlt->getUseFastVectorHighlighter());
        $this->assertEquals($options['usephrasehighlighter'], $this->_hlt->getUsePhraseHighlighter());
        $this->assertEquals($options['highlightmultiterm'], $this->_hlt->getHighlightMultiTerm());
        $this->assertEquals($options['regexslop'], $this->_hlt->getRegexSlop());
        $this->assertEquals($options['regexpattern'], $this->_hlt->getRegexPattern());
        $this->assertEquals($options['regexmaxanalyzedchars'], $this->_hlt->getRegexMaxAnalyzedChars());
        $this->assertEquals($options['query'], $this->_hlt->getQuery());
        $this->assertEquals($options['phraselimit'], $this->_hlt->getPhraseLimit());
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Query_Select::COMPONENT_HIGHLIGHTING, $this->_hlt->getType());
    }

    public function testGetFieldAutocreate()
    {
        $name = 'test';
        $field = $this->_hlt->getField($name);

        $this->assertEquals($name, $field->getName());
    }

    public function testGetFieldNoAutocreate()
    {
        $name = 'test';
        $field = $this->_hlt->getField($name, false);

        $this->assertEquals(null, $field);
    }

    public function testAddFieldWithObject()
    {
        $field = new Solarium_Query_Select_Component_Highlighting_Field;
        $field->setName('test');

        $this->_hlt->addField($field);

        $this->assertEquals($field, $this->_hlt->getField('test'));
    }

    public function testAddFieldWithString()
    {
        $name = 'test';
        $this->_hlt->addField($name);

        $this->assertEquals(array($name), array_keys($this->_hlt->getFields()));
    }

    public function testAddFieldWithArray()
    {
        $config = array(
            'name' => 'fieldA',
            'snippets' => 6
        );
        $this->_hlt->addField($config);

        $this->assertEquals(6, $this->_hlt->getField('fieldA')->getSnippets());
    }

    public function testAddFieldWithObjectWithoutName()
    {
        $field = new Solarium_Query_Select_Component_Highlighting_Field;
        $this->setExpectedException('Solarium_Exception');
        $this->_hlt->addField($field);
    }

    public function testAddsFieldsWithString()
    {
        $fields = 'test1,test2';
        $this->_hlt->addFields($fields);

        $this->assertEquals(array('test1','test2'), array_keys($this->_hlt->getFields()));
    }

    public function testAddsFieldsWithArray()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );
        $this->_hlt->addFields($fields);

        $this->assertEquals(2, $this->_hlt->getField('test1')->getSnippets());
        $this->assertEquals(5, $this->_hlt->getField('test2')->getSnippets());
    }

    public function testRemoveField()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->_hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->_hlt->getFields()));

        $this->_hlt->removeField('test1');
        $this->assertEquals(array('test2'), array_keys($this->_hlt->getFields()));
    }

    public function testRemoveFieldWithInvalidName()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->_hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->_hlt->getFields()));

        $this->_hlt->removeField('test1=3'); // should fail silently and do nothing
        $this->assertEquals(array('test1','test2'), array_keys($this->_hlt->getFields()));
    }

    public function testClearFields()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->_hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->_hlt->getFields()));

        $this->_hlt->clearFields();
        $this->assertEquals(array(), array_keys($this->_hlt->getFields()));
    }

    public function testSetFields()
    {
        $fields = array(
            'test1' => array('snippets' => 2),
            'test2' => array('snippets' => 5),
        );

        $this->_hlt->addFields($fields);
        $this->assertEquals(array('test1','test2'), array_keys($this->_hlt->getFields()));

        $newFields = array(
            'test3' => array('snippets' => 4),
            'test4' => array('snippets' => 6),
        );

        $this->_hlt->setFields($newFields);
        $this->assertEquals(array('test3','test4'), array_keys($this->_hlt->getFields()));
    }


    public function testSetAndGetSnippets()
    {
        $value = 2;
        $this->_hlt->setSnippets($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getSnippets()
        );
    }

    public function testSetAndGetFragSize()
    {
        $value = 20;
        $this->_hlt->setFragsize($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getFragSize()
        );
    }

    public function testSetAndGetMergeContiguous()
    {
        $value = true;
        $this->_hlt->setMergeContiguous($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getMergeContiguous()
        );
    }

    public function testSetAndGetRequireFieldMatch()
    {
        $value = true;
        $this->_hlt->setRequireFieldMatch($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getRequireFieldMatch()
        );
    }

    public function testSetAndGetMaxAnalyzedChars()
    {
        $value = 200;
        $this->_hlt->setMaxAnalyzedChars($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getMaxAnalyzedChars()
        );
    }

    public function testSetAndGetAlternateField()
    {
        $value = 'description';
        $this->_hlt->setAlternateField($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getAlternateField()
        );
    }

    public function testSetAndGetMaxAlternateFieldLength()
    {
        $value = 150;
        $this->_hlt->setMaxAlternateFieldLength($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getMaxAlternateFieldLength()
        );
    }

    public function testSetAndGetFormatter()
    {
        $this->_hlt->setFormatter();

        $this->assertEquals(
            'simple',
            $this->_hlt->getFormatter()
        );
    }

    public function testSetAndGetSimplePrefix()
    {
        $value = '<em>';
        $this->_hlt->setSimplePrefix($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getSimplePrefix()
        );
    }

    public function testSetAndGetSimplePostfix()
    {
        $value = '</em>';
        $this->_hlt->setSimplePostfix($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getSimplePostfix()
        );
    }

    public function testSetAndGetFragmenter()
    {
        $value = Solarium_Query_Select_Component_Highlighting::FRAGMENTER_REGEX;
        $this->_hlt->setFragmenter($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getFragmenter()
        );
    }

    public function testSetAndGetFragListBuilder()
    {
        $value = 'myBuilder';
        $this->_hlt->setFragListBuilder($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getFragListBuilder()
        );
    }

    public function testSetAndGetFragmentsBuilder()
    {
        $value = 'myBuilder';
        $this->_hlt->setFragmentsBuilder($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getFragmentsBuilder()
        );
    }

    public function testSetAndGetUseFastVectorHighlighter()
    {
        $value = true;
        $this->_hlt->setUseFastVectorHighlighter($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getUseFastVectorHighlighter()
        );
    }

    public function testSetAndGetUsePhraseHighlighter()
    {
        $value = true;
        $this->_hlt->setUsePhraseHighlighter($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getUsePhraseHighlighter()
        );
    }

    public function testSetAndGetHighlightMultiTerm()
    {
        $value = true;
        $this->_hlt->setHighlightMultiTerm($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getHighlightMultiTerm()
        );
    }

    public function testSetAndGetRegexSlop()
    {
        $value = .8;
        $this->_hlt->setRegexSlop($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getRegexSlop()
        );
    }

    public function testSetAndGetRegexPattern()
    {
        $value = 'myPattern';
        $this->_hlt->setRegexPattern($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getRegexPattern()
        );
    }

    public function testSetAndGetRegexMaxAnalyzedChars()
    {
        $value = 500;
        $this->_hlt->setRegexMaxAnalyzedChars($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getRegexMaxAnalyzedChars()
        );
    }

    public function testSetAndGetQuery()
    {
        $value = 'text:myvalue';
        $this->_hlt->setQuery($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getQuery()
        );
    }

    public function testSetAndGetPhraseLimit()
    {
        $value = 20;
        $this->_hlt->setPhraseLimit($value);

        $this->assertEquals(
            $value,
            $this->_hlt->getPhraseLimit()
        );
    }

}
