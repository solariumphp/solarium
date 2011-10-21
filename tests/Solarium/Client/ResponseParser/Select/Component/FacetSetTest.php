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

class Solarium_Client_ResponseParser_Select_Component_FacetSetTest extends PHPUnit_Framework_TestCase
{

    protected $_parser, $_facetSet;

    public function setUp()
    {
        $this->_parser = new Solarium_Client_ResponseParser_Select_Component_FacetSet;

        $this->_facetSet = new Solarium_Query_Select_Component_FacetSet();
        $this->_facetSet->createFacet('field', array('key' => 'keyA', 'field' => 'fieldA'));
        $this->_facetSet->createFacet('query', array('key' => 'keyB'));
        $this->_facetSet->createFacet('multiquery', array('key' => 'keyC', 'query' => array('keyC_A' => array('query' => 'id:1'), 'keyC_B' => array('query' => 'id:2'))));
        $this->_facetSet->createFacet('range', array('key' => 'keyD'));
    }

    public function testParse()
    {
        $data = array(
            'facet_counts' =>  array(
                'facet_fields' => array(
                    'keyA' => array(
                        'value1',
                        12,
                        'value2',
                        3,
                    ),
                ),
                'facet_queries' => array(
                    'keyB' => 23,
                    'keyC_A' => 25,
                    'keyC_B' => 16,
                ),
                'facet_ranges' => array(
                    'keyD' => array(
                        'before' => 3,
                        'after' => 5,
                        'between' => 4,
                        'counts' => array(
                            '1.0',
                            1,
                            '101.0',
                            2,
                            '201.0',
                            1,
                        )
                    )
                )
            )
        );

        $result = $this->_parser->parse(null, $this->_facetSet, $data);
        $facets = $result->getFacets();

        $this->assertEquals(array('keyA','keyB','keyC','keyD'), array_keys($facets));

        $this->assertEquals(
            array('value1' => 12, 'value2' => 3),
            $facets['keyA']->getValues()
        );

        $this->assertEquals(
            23,
            $facets['keyB']->getValue()
        );

        $this->assertEquals(
            array('keyC_A' => 25, 'keyC_B' => 16),
            $facets['keyC']->getValues()
        );

        $this->assertEquals(
            array('1.0' => 1, '101.0' => 2, '201.0' => 1),
            $facets['keyD']->getValues()
        );

        $this->assertEquals(
            3,
            $facets['keyD']->getBefore()
        );

        $this->assertEquals(
            4,
            $facets['keyD']->getBetween()
        );

        $this->assertEquals(
            5,
            $facets['keyD']->getAfter()
        );
    }

    public function testParseNoData()
    {
        $result = $this->_parser->parse(null, $this->_facetSet, array());
        $this->assertEquals(array(), $result->getFacets());
    }

    public function testInvalidFacetType()
    {
        $facetStub = $this->getMock('Solarium_Query_Select_Component_Facet_Field');
        $facetStub->expects($this->once())
             ->method('getType')
             ->will($this->returnValue('invalidfacettype'));
        $facetStub->expects($this->any())
             ->method('getKey')
             ->will($this->returnValue('facetkey'));

        $this->_facetSet->addFacet($facetStub);

        $this->setExpectedException('Solarium_Exception');
        $this->_parser->parse(null, $this->_facetSet, array());
    }

}
