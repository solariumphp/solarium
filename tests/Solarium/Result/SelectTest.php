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

class Solarium_Result_SelectTest extends Solarium_Result_QueryTypeTest
{
    /*
    protected $_result, $_docs, $_facets;

    public function setUp()
    {
        $this->_docs = array(
            new Solarium_Document_ReadOnly(array('id'=>1,'name'=>'test1')),
            new Solarium_Document_ReadOnly(array('id'=>2,'name'=>'test2')),
            new Solarium_Document_ReadOnly(array('id'=>3,'name'=>'test3')),
        );

        $this->_facets = array(
            'f1' => new Solarium_Result_Select_Facet_Field(array('a' => 14)),
            'f2' => new Solarium_Result_Select_Facet_Field(array('b' => 5)),
        );

        $this->_components = array(
            Solarium_Query_Select::COMPONENT_MORELIKETHIS => new Solarium_Result_Select_MoreLikeThis(array())
        );                

        $this->_result = new Solarium_Result_Select(0,45,100, $this->_docs, $this->_facets, $this->_components);
    }

    public function testGetNumFound()
    {
        $this->assertEquals(100, $this->_result->getNumFound());
    }

    public function testGetDocuments()
    {
        $this->assertEquals($this->_docs, $this->_result->getDocuments());
    }

    public function testGetFacets()
    {
        $this->assertEquals($this->_facets, $this->_result->getFacets());
    }

    public function testGetFacetByKey()
    {
        $this->assertEquals($this->_facets['f2'], $this->_result->getFacet('f2'));
    }

    public function testGetFacetByInvalidKey()
    {
        $this->assertEquals(null, $this->_result->getFacet('f2123123'));
    }

    public function testCount()
    {
        $this->assertEquals(3, count($this->_result));
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

    public function testIterator()
    {
        $docs = array();
        foreach($this->_result AS $key => $doc)
        {
            $docs[$key] = $doc;
        }

        $this->assertEquals($this->_docs, $docs);
    }
    */
}
