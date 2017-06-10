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

namespace Solarium\Tests\QueryType\Select\ResponseParser\Component;

use Solarium\QueryType\Select\ResponseParser\Component\FacetSet as Parser;
use Solarium\QueryType\Select\Query\Component\FacetSet;
use Solarium\QueryType\Select\Query\Query;

class FacetSetTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;
    protected $facetSet;
    protected $query;

    public function setUp()
    {
        $this->parser = new Parser;

        $this->facetSet = new FacetSet();
        $this->facetSet->createFacet('field', array('key' => 'keyA', 'field' => 'fieldA'));
        $this->facetSet->createFacet('query', array('key' => 'keyB'));
        $this->facetSet->createFacet(
            'multiquery',
            array(
                'key' => 'keyC',
                'query' => array(
                    'keyC_A' => array('query' => 'id:1'),
                    'keyC_B' => array('query' => 'id:2'),
                ),
            )
        );
        $this->facetSet->createFacet('range', array('key' => 'keyD'));
        $this->facetSet->createFacet('pivot', array('key' => 'keyE', 'fields' => 'cat,price'));

        $this->query = new Query;
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
                ),
                'facet_pivot' => array(
                    'keyE' => array(
                        array(
                            'field' => 'cat',
                            'value' => 'abc',
                            'count' => '123',
                            'pivot' => array(
                                array('field' => 'price', 'value' => 1, 'count' => 12),
                                array('field' => 'price', 'value' => 2, 'count' => 8),
                            )
                        )
                    ),
                ),
            )
        );

        $result = $this->parser->parse($this->query, $this->facetSet, $data);
        $facets = $result->getFacets();

        $this->assertEquals(array('keyA', 'keyB', 'keyC', 'keyD', 'keyE'), array_keys($facets));

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

        $this->assertEquals(
            1,
            count($facets['keyE'])
        );

        $this->query = new Query;
    }

    public function testParseExtractFromResponse()
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
                ),
                'facet_pivot' => array(
                    'cat,price' => array(
                        array(
                            'field' => 'cat',
                            'value' => 'abc',
                            'count' => '123',
                            'pivot' => array(
                                array('field' => 'price', 'value' => 1, 'count' => 12),
                                array('field' => 'price', 'value' => 2, 'count' => 8),
                            ),
                            'stats' => array(
                                'min' => 4,
                                'max' => 6,
                            )
                        )
                    ),
                ),
            )
        );

        $facetSet = new FacetSet();
        $facetSet->setExtractFromResponse(true);

        $result = $this->parser->parse($this->query, $facetSet, $data);
        $facets = $result->getFacets();

        $this->assertEquals(array('keyA', 'keyB', 'keyC_A', 'keyC_B', 'keyD', 'cat,price'), array_keys($facets));

        $this->assertEquals(
            array('value1' => 12, 'value2' => 3),
            $facets['keyA']->getValues()
        );

        $this->assertEquals(
            23,
            $facets['keyB']->getValue()
        );

        // As the multiquery facet is a Solarium virtual facet type, it cannot be detected based on Solr response data
        $this->assertEquals(
            25,
            $facets['keyC_A']->getValue()
        );

        $this->assertEquals(
            16,
            $facets['keyC_B']->getValue()
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

        $this->assertEquals(
            1,
            count($facets['cat,price'])
        );

        $pivots = $facets['cat,price']->getPivot();

        $this->assertEquals(
            2,
            count($pivots[0]->getStats())
        );

        $this->query = new Query;
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, $this->facetSet, array());
        $this->assertEquals(array(), $result->getFacets());
    }

    public function testInvalidFacetType()
    {
        $facetStub = $this->getMock('Solarium\QueryType\Select\Query\Component\Facet\Field');
        $facetStub->expects($this->once())
             ->method('getType')
             ->will($this->returnValue('invalidfacettype'));
        $facetStub->expects($this->any())
             ->method('getKey')
             ->will($this->returnValue('facetkey'));

        $this->facetSet->addFacet($facetStub);

        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $this->parser->parse($this->query, $this->facetSet, array());
    }
}
