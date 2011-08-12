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

class Solarium_Query_Select_Component_Facet_MultiQueryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Facet_MultiQuery
     */
    protected $_facet;

    public function setUp()
    {
        $this->_facet = new Solarium_Query_Select_Component_Facet_MultiQuery;
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1','e2'),
            'query' => array(
                array(
                    'key' => 'k1',
                    'query' => 'category:1',
                    'exclude' => array('fq1','fq2')
                ),
                'k2' => array(
                    'query' => 'category:2'
                ),
            )
        );

        $this->_facet->setOptions($options);

        $this->assertEquals($options['key'], $this->_facet->getKey());
        $this->assertEquals($options['exclude'], $this->_facet->getExcludes());

        $query1 = $this->_facet->getQuery('k1');
        $this->assertEquals('k1', $query1->getKey());
        $this->assertEquals('category:1', $query1->getQuery());
        $this->assertEquals(array('fq1','fq2', 'e1', 'e2'), $query1->getExcludes());

        $query2 = $this->_facet->getQuery('k2');
        $this->assertEquals('k2', $query2->getKey());
        $this->assertEquals('category:2', $query2->getQuery());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Solarium_Query_Select_Component_FacetSet::FACET_MULTIQUERY,
            $this->_facet->getType()
        );
    }

    public function testCreateAndGetQuery()
    {
        $key = 'k1';
        $query = 'category:1';
        $excludes = array('fq1','fq2');

        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->_facet->createQuery($key, $query, $excludes);

        $this->assertEquals(
            $facetQuery,
            $this->_facet->getQuery($key)
        );
    }

    public function testAddAndGetQuery()
    {
        $key = 'k1';
        $query = 'category:1';
        $excludes = array('fq1','fq2');

        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey($key);
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->_facet->addQuery($facetQuery);

        $this->assertEquals(
            $facetQuery,
            $this->_facet->getQuery($key)
        );
    }

    public function testAddQueryWithConfig()
    {
        $config = array(
            'key' => 'k1',
            'query' => 'category:1',
            'excludes' => array('fq1','fq2')
        );

        $facetQuery = new Solarium_Query_Select_Component_Facet_Query($config);

        $this->_facet->addQuery($config);

        $this->assertEquals(
            $facetQuery,
            $this->_facet->getQuery($config['key'])
        );
    }

    public function testAddQueryNoKey()
    {
        $query = 'category:1';
        $excludes = array('fq1','fq2');
        
        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setQuery($query);
        $facetQuery->setExcludes($excludes);

        $this->setExpectedException('Solarium_Exception');
        $this->_facet->addQuery($facetQuery);
    }

    public function testAddQueryNoUniqueKey()
    {
        $facetQuery1 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery2->setKey('k1');
        $facetQuery2->setQuery('category:2');

        $this->_facet->addQuery($facetQuery1);

        $this->setExpectedException('Solarium_Exception');
        $this->_facet->addQuery($facetQuery2);
    }


    public function testAddQueryExcludeForwarding()
    {
        $this->_facet->addExclude('fq1');

        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->_facet->addQuery($facetQuery);
        
        $this->assertEquals(
            array('fq1'),
            $facetQuery->getExcludes()
        );
    }

    public function testAddAndGetQueries()
    {
        $facetQuery1 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $this->_facet->addQueries(array($facetQuery1, $facetQuery2));

        $this->assertEquals(
            array('k1' => $facetQuery1, 'k2' => $facetQuery2),
            $this->_facet->getQueries()
        );
    }

    public function testAddQueriesWithConfig()
    {
        $facetQuery1 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');
        $facetQuery1->addExcludes(array('fq1','fq2'));

        $facetQuery2 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $facetQueries = array('k1' => $facetQuery1, 'k2' => $facetQuery2);

        $config = array(
            array(
                'key' => 'k1',
                'query' => 'category:1',
                'exclude' => array('fq1','fq2')
            ),
            'k2' => array(
                'query' => 'category:2'
            ),
        );
        $this->_facet->addQueries($config);

        $this->assertEquals(
            $facetQueries,
            $this->_facet->getQueries()
        );
    }

    public function testGetInvalidQuery()
    {
        $this->assertEquals(
            null,
            $this->_facet->getQuery('invalidkey')
        );
    }

    public function testRemoveQuery()
    {
        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->_facet->addQuery($facetQuery);
        $this->assertEquals(
            array('k1' => $facetQuery),
            $this->_facet->getQueries()
        );

        $this->_facet->removeQuery('k1');
        $this->assertEquals(
            array(),
            $this->_facet->getQueries()
        );
    }

    public function testRemoveQueryWithObjectInput()
    {
        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');

        $this->_facet->addQuery($facetQuery);
        $this->assertEquals(
            array('k1' => $facetQuery),
            $this->_facet->getQueries()
        );

        $this->_facet->removeQuery($facetQuery);
        $this->assertEquals(
            array(),
            $this->_facet->getQueries()
        );
    }

    public function testRemoveInvalidQuery()
    {
        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->_facet->addQuery($facetQuery);

        $before = $this->_facet->getQueries();
        $this->_facet->removeQuery('invalidkey');
        $after = $this->_facet->getQueries();

        $this->assertEquals(
            $before,
            $after
        );
    }

    public function testClearQueries()
    {
        $facetQuery1 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');

        $facetQuery2 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $this->_facet->addQueries(array($facetQuery1, $facetQuery2));
        $this->_facet->clearQueries();
        $this->assertEquals(
            array(),
            $this->_facet->getQueries()
        );
    }

    public function testSetQueries()
    {
        $facetQuery1 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery1->setKey('k1');
        $facetQuery1->setQuery('category:1');
        $this->_facet->addQuery($facetQuery1);

        $facetQuery2 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery2->setKey('k2');
        $facetQuery2->setQuery('category:2');

        $facetQuery3 = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery3->setKey('k3');
        $facetQuery3->setQuery('category:3');

        $this->_facet->setQueries(array($facetQuery2, $facetQuery3));
        $this->assertEquals(
            array('k2' => $facetQuery2, 'k3' => $facetQuery3),
            $this->_facet->getQueries()
        );
    }

    public function testAddExcludeForwarding()
    {
        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->_facet->addQuery($facetQuery);

        $this->_facet->addExclude('fq1');

        $this->assertEquals(
            array('fq1'),
            $facetQuery->getExcludes()
        );
    }

    public function testRemoveExcludeForwarding()
    {
        $this->_facet->addExclude('fq1');

        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->_facet->addQuery($facetQuery);

        $this->assertEquals(
            array('fq1'),
            $facetQuery->getExcludes()
        );

        $this->_facet->removeExclude('fq1');

        $this->assertEquals(
            array(),
            $facetQuery->getExcludes()
        );
    }

    public function testClearExcludesForwarding()
    {
        $this->_facet->addExclude('fq1');
        $this->_facet->addExclude('fq2');

        $facetQuery = new Solarium_Query_Select_Component_Facet_Query;
        $facetQuery->setKey('k1');
        $facetQuery->setQuery('category:1');
        $this->_facet->addQuery($facetQuery);

        $this->assertEquals(
            array('fq1','fq2'),
            $facetQuery->getExcludes()
        );

        $this->_facet->clearExcludes();

        $this->assertEquals(
            array(),
            $facetQuery->getExcludes()
        );
    }


}
