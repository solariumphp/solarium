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

class Solarium_Query_Select_Component_FacetSetTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_FacetSet
     */
    protected $_facetSet;

    public function setUp()
    {
        $this->_facetSet = new Solarium_Query_Select_Component_FacetSet;
    }

    public function testConfigMode()
    {
        $options = array(
            'facet' => array(
                array('type' => 'query', 'key' => 'f1', 'query' => 'category:1'),
                'f2' => array('type' => 'query', 'query' => 'category:2')
            ),
            'prefix' => 'pr',
            'sort' => 'index',
            'mincount' => 10,
            'missing' => 5,
        );

        $this->_facetSet->setOptions($options);
        $facets = $this->_facetSet->getFacets();

        $this->assertEquals(2, count($facets));
        $this->assertEquals($options['prefix'], $this->_facetSet->getPrefix());
        $this->assertEquals($options['sort'], $this->_facetSet->getSort());
        $this->assertEquals($options['mincount'], $this->_facetSet->getMincount());
        $this->assertEquals($options['missing'], $this->_facetSet->getMissing());
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Query_Select::COMPONENT_FACETSET, $this->_facetSet->getType());
    }

    public function testSetAndGetSort()
    {
        $this->_facetSet->setSort('index');
        $this->assertEquals('index', $this->_facetSet->getSort());
    }

    public function testSetAndGetPrefix()
    {
        $this->_facetSet->setPrefix('xyz');
        $this->assertEquals('xyz', $this->_facetSet->getPrefix());
    }

    public function testSetAndGetLimit()
    {
        $this->_facetSet->setLimit(12);
        $this->assertEquals(12, $this->_facetSet->getLimit());
    }

    public function testSetAndGetMinCount()
    {
        $this->_facetSet->setMincount(100);
        $this->assertEquals(100, $this->_facetSet->getMincount());
    }

    public function testSetAndGetMissing()
    {
        $this->_facetSet->setMissing(true);
        $this->assertEquals(true, $this->_facetSet->getMissing());
    }

    public function testAddAndGetFacet()
    {
        $fq = new Solarium_Query_Select_Component_Facet_Query;
        $fq->setKey('f1')->setQuery('category:1');
        $this->_facetSet->addFacet($fq);

        $this->assertEquals(
            $fq,
            $this->_facetSet->getFacet('f1')
        );
    }

    public function testAddFacetWithoutKey()
    {
        $fq = new Solarium_Query_Select_Component_Facet_Query;
        $fq->setQuery('category:1');

        $this->setExpectedException('Solarium_Exception');
        $this->_facetSet->addFacet($fq);
    }

    public function testAddFacetWithUsedKey()
    {
        $fq1 = new Solarium_Query_Select_Component_Facet_Query;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_Component_Facet_Query;
        $fq2->setKey('f1')->setQuery('category:2');

        $this->_facetSet->addFacet($fq1);
        $this->setExpectedException('Solarium_Exception');
        $this->_facetSet->addFacet($fq2);
    }

    public function testGetInvalidFacet()
    {
        $this->assertEquals(
            null,
            $this->_facetSet->getFacet('invalidtag')
        );
    }

    public function testAddFacets()
    {
        $fq1 = new Solarium_Query_Select_Component_Facet_Query;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_Component_Facet_Query;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->_facetSet->addFacets($facets);
        $this->assertEquals(
            $facets,
            $this->_facetSet->getFacets()
        );
    }

    public function testAddFacetsWithConfig()
    {
        $facets = array(
            array('type' => 'query', 'key' => 'f1', 'query' => 'category:1'),
            'f2' => array('type' => 'query', 'query' => 'category:2')
        );

        $this->_facetSet->addFacets($facets);

        $this->assertEquals(
            2,
            count($this->_facetSet->getFacets())
        );
    }

    public function testRemoveFacet()
    {
        $fq1 = new Solarium_Query_Select_Component_Facet_Query;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_Component_Facet_Query;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->_facetSet->addFacets($facets);
        $this->_facetSet->removeFacet('f1');
        $this->assertEquals(
            array('f2' => $fq2),
            $this->_facetSet->getFacets()
        );
    }

    public function testRemoveFacetWithObjectInput()
    {
        $fq1 = new Solarium_Query_Select_Component_Facet_Query;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_Component_Facet_Query;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->_facetSet->addFacets($facets);
        $this->_facetSet->removeFacet($fq1);
        $this->assertEquals(
            array('f2' => $fq2),
            $this->_facetSet->getFacets()
        );
    }

    public function testRemoveInvalidFacet()
    {
        $fq1 = new Solarium_Query_Select_Component_Facet_Query;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_Component_Facet_Query;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->_facetSet->addFacets($facets);
        $this->_facetSet->removeFacet('f3'); //continue silently
        $this->assertEquals(
            $facets,
            $this->_facetSet->getFacets()
        );
    }

    public function testClearFacets()
    {
        $fq1 = new Solarium_Query_Select_Component_Facet_Query;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_Component_Facet_Query;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->_facetSet->addFacets($facets);
        $this->_facetSet->clearFacets();
        $this->assertEquals(
            array(),
            $this->_facetSet->getFacets()
        );
    }

    public function testSetFacets()
    {
        $fq1 = new Solarium_Query_Select_Component_Facet_Query;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_Component_Facet_Query;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->_facetSet->addFacets($facets);

        $fq3 = new Solarium_Query_Select_Component_Facet_Query;
        $fq3->setKey('f3')->setQuery('category:3');

        $fq4 = new Solarium_Query_Select_Component_Facet_Query;
        $fq4->setKey('f4')->setQuery('category:4');

        $facets = array('f3' => $fq3, 'f4' => $fq4);

        $this->_facetSet->setFacets($facets);

        $this->assertEquals(
            $facets,
            $this->_facetSet->getFacets()
        );
    }

    public function testCreateFacet()
    {
        $type = Solarium_Query_Select_Component_FacetSet::FACET_FIELD;
        $options = array('optionA' => 1, 'optionB' => 2);
        $facet = $this->_facetSet->createFacet($type, $options);

        // check class mapping
        $this->assertEquals(
            $type,
            $facet->getType()
        );

        // check option forwarding
        $facetOptions = $facet->getOptions();
        $this->assertEquals(
            $options['optionB'],
            $facetOptions['optionB']
        );
    }

    public function testCreateFacetAdd()
    {
        $type = Solarium_Query_Select_Component_FacetSet::FACET_FIELD;
        $options = array('key' => 'mykey','optionA' => 1, 'optionB' => 2);
        $facet = $this->_facetSet->createFacet($type, $options);

        $this->assertEquals($facet, $this->_facetSet->getFacet('mykey'));
    }

    public function testCreateFacetAddWithString()
    {
        $type = Solarium_Query_Select_Component_FacetSet::FACET_FIELD;
        $options = 'mykey';
        $facet = $this->_facetSet->createFacet($type, $options);

        $this->assertEquals($facet, $this->_facetSet->getFacet('mykey'));
    }

    public function testCreateFacetWithInvalidType()
    {
        $this->setExpectedException('Solarium_Exception');
        $this->_facetSet->createFacet('invalidtype');
    }

    public function testCreateFacetField()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Query_Select_Component_FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(Solarium_Query_Select_Component_FacetSet::FACET_FIELD), $this->equalTo($options));

        $observer->createFacetField($options);
    }

    public function testCreateFacetQuery()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Query_Select_Component_FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(Solarium_Query_Select_Component_FacetSet::FACET_QUERY), $this->equalTo($options));

        $observer->createFacetQuery($options);
    }

    public function testCreateFacetMultiQuery()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Query_Select_Component_FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(Solarium_Query_Select_Component_FacetSet::FACET_MULTIQUERY), $this->equalTo($options));

        $observer->createFacetMultiQuery($options);
    }

    public function testCreateFacetRange()
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium_Query_Select_Component_FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(Solarium_Query_Select_Component_FacetSet::FACET_RANGE), $this->equalTo($options));

        $observer->createFacetRange($options);
    }
    
}
