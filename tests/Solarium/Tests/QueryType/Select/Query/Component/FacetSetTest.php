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

namespace Solarium\Tests\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Component\FacetSet;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Query\Component\Facet\Query as FacetQuery;

class FacetSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FacetSet
     */
    protected $facetSet;

    public function setUp()
    {
        $this->facetSet = new FacetSet;
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
            'extractfromresponse' => true,
            'contains' => 'foobar',
            'containsignorecase' => true,
        );

        $this->facetSet->setOptions($options);
        $facets = $this->facetSet->getFacets();

        $this->assertEquals(2, count($facets));
        $this->assertEquals($options['prefix'], $this->facetSet->getPrefix());
        $this->assertEquals($options['sort'], $this->facetSet->getSort());
        $this->assertEquals($options['mincount'], $this->facetSet->getMincount());
        $this->assertEquals($options['missing'], $this->facetSet->getMissing());
        $this->assertEquals($options['extractfromresponse'], $this->facetSet->getExtractFromResponse());
        $this->assertEquals($options['contains'], $this->facetSet->getContains());
        $this->assertEquals($options['containsignorecase'], $this->facetSet->getContainsIgnoreCase());
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMPONENT_FACETSET, $this->facetSet->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser\Component\FacetSet',
            $this->facetSet->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\FacetSet',
            $this->facetSet->getRequestBuilder()
        );
    }

    public function testSetAndGetSort()
    {
        $this->facetSet->setSort('index');
        $this->assertEquals('index', $this->facetSet->getSort());
    }

    public function testSetAndGetPrefix()
    {
        $this->facetSet->setPrefix('xyz');
        $this->assertEquals('xyz', $this->facetSet->getPrefix());
    }

    public function testSetAndGetLimit()
    {
        $this->facetSet->setLimit(12);
        $this->assertEquals(12, $this->facetSet->getLimit());
    }

    public function testSetAndGetMinCount()
    {
        $this->facetSet->setMincount(100);
        $this->assertEquals(100, $this->facetSet->getMincount());
    }

    public function testSetAndGetMissing()
    {
        $this->facetSet->setMissing(true);
        $this->assertEquals(true, $this->facetSet->getMissing());
    }

    public function testAddAndGetFacet()
    {
        $fq = new FacetQuery;
        $fq->setKey('f1')->setQuery('category:1');
        $this->facetSet->addFacet($fq);

        $this->assertEquals(
            $fq,
            $this->facetSet->getFacet('f1')
        );
    }

    public function testAddFacetWithoutKey()
    {
        $fq = new FacetQuery;
        $fq->setQuery('category:1');

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->facetSet->addFacet($fq);
    }

    public function testAddFacetWithUsedKey()
    {
        $fq1 = new FacetQuery;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery;
        $fq2->setKey('f1')->setQuery('category:2');

        $this->facetSet->addFacet($fq1);
        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->facetSet->addFacet($fq2);
    }

    public function testGetInvalidFacet()
    {
        $this->assertEquals(
            null,
            $this->facetSet->getFacet('invalidtag')
        );
    }

    public function testAddFacets()
    {
        $fq1 = new FacetQuery;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->facetSet->addFacets($facets);
        $this->assertEquals(
            $facets,
            $this->facetSet->getFacets()
        );
    }

    public function testAddFacetsWithConfig()
    {
        $facets = array(
            array('type' => 'query', 'key' => 'f1', 'query' => 'category:1'),
            'f2' => array('type' => 'query', 'query' => 'category:2')
        );

        $this->facetSet->addFacets($facets);

        $this->assertEquals(
            2,
            count($this->facetSet->getFacets())
        );
    }

    public function testRemoveFacet()
    {
        $fq1 = new FacetQuery;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->facetSet->addFacets($facets);
        $this->facetSet->removeFacet('f1');
        $this->assertEquals(
            array('f2' => $fq2),
            $this->facetSet->getFacets()
        );
    }

    public function testRemoveFacetWithObjectInput()
    {
        $fq1 = new FacetQuery;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->facetSet->addFacets($facets);
        $this->facetSet->removeFacet($fq1);
        $this->assertEquals(
            array('f2' => $fq2),
            $this->facetSet->getFacets()
        );
    }

    public function testRemoveInvalidFacet()
    {
        $fq1 = new FacetQuery;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->facetSet->addFacets($facets);
        $this->facetSet->removeFacet('f3'); //continue silently
        $this->assertEquals(
            $facets,
            $this->facetSet->getFacets()
        );
    }

    public function testClearFacets()
    {
        $fq1 = new FacetQuery;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->facetSet->addFacets($facets);
        $this->facetSet->clearFacets();
        $this->assertEquals(
            array(),
            $this->facetSet->getFacets()
        );
    }

    public function testSetFacets()
    {
        $fq1 = new FacetQuery;
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery;
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = array('f1' => $fq1, 'f2' => $fq2);

        $this->facetSet->addFacets($facets);

        $fq3 = new FacetQuery;
        $fq3->setKey('f3')->setQuery('category:3');

        $fq4 = new FacetQuery;
        $fq4->setKey('f4')->setQuery('category:4');

        $facets = array('f3' => $fq3, 'f4' => $fq4);

        $this->facetSet->setFacets($facets);

        $this->assertEquals(
            $facets,
            $this->facetSet->getFacets()
        );
    }

    public function testCreateFacet()
    {
        $type = FacetSet::FACET_FIELD;
        $options = array('optionA' => 1, 'optionB' => 2);
        $facet = $this->facetSet->createFacet($type, $options);

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
        $type = FacetSet::FACET_FIELD;
        $options = array('key' => 'mykey', 'optionA' => 1, 'optionB' => 2);
        $facet = $this->facetSet->createFacet($type, $options);

        $this->assertEquals($facet, $this->facetSet->getFacet('mykey'));
    }

    public function testCreateFacetAddWithString()
    {
        $type = FacetSet::FACET_FIELD;
        $options = 'mykey';
        $facet = $this->facetSet->createFacet($type, $options);

        $this->assertEquals($facet, $this->facetSet->getFacet('mykey'));
    }

    public function testCreateFacetWithInvalidType()
    {
        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
        $this->facetSet->createFacet('invalidtype');
    }

    public function createFacetAddProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetField($add)
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\QueryType\Select\Query\Component\FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(FacetSet::FACET_FIELD), $this->equalTo($options), $add);

        $observer->createFacetField($options, $add);
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetQuery($add)
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\QueryType\Select\Query\Component\FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(FacetSet::FACET_QUERY), $this->equalTo($options), $add);

        $observer->createFacetQuery($options, $add);
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetMultiQuery($add)
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\QueryType\Select\Query\Component\FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(FacetSet::FACET_MULTIQUERY), $this->equalTo($options), $add);

        $observer->createFacetMultiQuery($options, $add);
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetRange($add)
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\QueryType\Select\Query\Component\FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(FacetSet::FACET_RANGE), $this->equalTo($options), $add);

        $observer->createFacetRange($options, $add);
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetPivot($add)
    {
        $options = array('optionA' => 1, 'optionB' => 2);

        $observer = $this->getMock('Solarium\QueryType\Select\Query\Component\FacetSet', array('createFacet'));
        $observer->expects($this->once())
                 ->method('createFacet')
                 ->with($this->equalTo(FacetSet::FACET_PIVOT), $this->equalTo($options), $add);

        $observer->createFacetPivot($options, $add);
    }

    public function testSetAndGetExtractFromResponse()
    {
        $this->facetSet->setExtractFromResponse(true);
        $this->assertEquals(true, $this->facetSet->getExtractFromResponse());
    }
}
