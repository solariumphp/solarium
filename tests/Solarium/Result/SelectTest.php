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

class Solarium_Result_SelectTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Select
     */
    protected $_result;

    protected $_numFound, $_docs, $_components, $_facetSet, $_moreLikeThis,
              $_highlighting, $_grouping, $_stats, $_debug;

    public function setUp()
    {
        $this->_numFound = 11;

        $this->_docs = array(
            new Solarium_Document_ReadOnly(array('id'=>1,'title'=>'doc1')),
            new Solarium_Document_ReadOnly(array('id'=>1,'title'=>'doc1')),
        );

        $this->_facetSet = 'dummy-facetset-value';
        $this->_moreLikeThis = 'dummy-facetset-value';
        $this->_highlighting = 'dummy-highlighting-value';
        $this->_grouping = 'dummy-grouping-value';
        $this->_spellcheck = 'dummy-grouping-value';
        $this->_stats = 'dummy-stats-value';
        $this->_debug = 'dummy-debug-value';

        $this->_components = array(
            Solarium_Query_Select::COMPONENT_FACETSET => $this->_facetSet,
            Solarium_Query_Select::COMPONENT_MORELIKETHIS => $this->_moreLikeThis,
            Solarium_Query_Select::COMPONENT_HIGHLIGHTING => $this->_highlighting,
            Solarium_Query_Select::COMPONENT_GROUPING => $this->_grouping,
            Solarium_Query_Select::COMPONENT_SPELLCHECK => $this->_spellcheck,
            Solarium_Query_Select::COMPONENT_STATS => $this->_stats,
            Solarium_Query_Select::COMPONENT_DEBUG => $this->_debug,
        );

        $this->_result = new Solarium_Result_SelectDummy(1, 12, $this->_numFound, $this->_docs, $this->_components);
    }

    public function testGetNumFound()
    {
        $this->assertEquals($this->_numFound, $this->_result->getNumFound());
    }

    public function testGetDocuments()
    {
        $this->assertEquals($this->_docs, $this->_result->getDocuments());
    }

    public function testGetFacetSet()
    {
        $this->assertEquals($this->_facetSet, $this->_result->getFacetSet());
    }

    public function testCount()
    {
        $this->assertEquals(count($this->_docs), count($this->_result));
    }

    public function testGetComponents()
    {
        $this->assertEquals($this->_components, $this->_result->getComponents());
    }

    public function testGetComponent()
    {
        $this->assertEquals(
            $this->_components[Solarium_Query_Select::COMPONENT_MORELIKETHIS],
            $this->_result->getComponent(Solarium_Query_Select::COMPONENT_MORELIKETHIS)
        );
    }

    public function testGetInvalidComponent()
    {
        $this->assertEquals(
            null,
            $this->_result->getComponent('invalid')
        );
    }

    public function testGetMoreLikeThis()
    {
        $this->assertEquals(
            $this->_components[Solarium_Query_Select::COMPONENT_MORELIKETHIS],
            $this->_result->getMoreLikeThis()
        );
    }

    public function testGetHighlighting()
    {
        $this->assertEquals(
            $this->_components[Solarium_Query_Select::COMPONENT_HIGHLIGHTING],
            $this->_result->getHighlighting()
        );
    }

    public function testGetGrouping()
    {
        $this->assertEquals(
            $this->_components[Solarium_Query_Select::COMPONENT_GROUPING],
            $this->_result->getGrouping()
        );
    }

    public function testGetSpellcheck()
    {
        $this->assertEquals(
            $this->_components[Solarium_Query_Select::COMPONENT_SPELLCHECK],
            $this->_result->getSpellcheck()
        );
    }

    public function testGetStats()
    {
        $this->assertEquals(
            $this->_components[Solarium_Query_Select::COMPONENT_STATS],
            $this->_result->getStats()
        );
    }

    public function testGetDebug()
    {
        $this->assertEquals(
            $this->_components[Solarium_Query_Select::COMPONENT_DEBUG],
            $this->_result->getDebug()
        );
    }

    public function testIterator()
    {
        $docs = array();
        foreach($this->_result AS $key => $doc)
        {
            $docs[$key] = $doc;
        }

        $this->assertEquals($this->_docs, $docs);
    }

    public function testGetStatus()
    {
        $this->assertEquals(
            1,
            $this->_result->getStatus()
        );
    }

    public function testGetQueryTime()
    {
        $this->assertEquals(
            12,
            $this->_result->getQueryTime()
        );
    }

}

class Solarium_Result_SelectDummy extends Solarium_Result_Select
{
    protected $_parsed = true;

    public function __construct($status, $queryTime, $numfound, $docs, $components)
    {
        $this->_numfound = $numfound;
        $this->_documents = $docs;
        $this->_components = $components;
        $this->_queryTime = $queryTime;
        $this->_status = $status;
    }

}