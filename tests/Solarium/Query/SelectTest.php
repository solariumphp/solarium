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

class Solarium_Query_SelectTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select
     */
    protected $_query;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Select;
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Client::QUERYTYPE_SELECT, $this->_query->getType());
    }

    public function testSetAndGetStart()
    {
        $this->_query->setStart(234);
        $this->assertEquals(234, $this->_query->getStart());
    }

    public function testSetAndGetQueryWithTrim()
    {
        $this->_query->setQuery(' *:* ');
        $this->assertEquals('*:*', $this->_query->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->_query->setQuery('id:%1%', array(678));
        $this->assertEquals('id:678', $this->_query->getQuery());
    }

    public function testSetAndGetQueryDefaultOperator()
    {
        $value = Solarium_Query_Select::QUERY_OPERATOR_AND;

        $this->_query->setQueryDefaultOperator($value);
        $this->assertEquals($value, $this->_query->getQueryDefaultOperator());
    }

    public function testSetAndGetQueryDefaultField()
    {
        $value = 'mydefault';

        $this->_query->setQueryDefaultField($value);
        $this->assertEquals($value, $this->_query->getQueryDefaultField());
    }

    public function testSetAndGetResultClass()
    {
        $this->_query->setResultClass('MyResult');
        $this->assertEquals('MyResult', $this->_query->getResultClass());
    }

    public function testSetAndGetDocumentClass()
    {
        $this->_query->setDocumentClass('MyDocument');
        $this->assertEquals('MyDocument', $this->_query->getDocumentClass());
    }

    public function testSetAndGetRows()
    {
        $this->_query->setRows(100);
        $this->assertEquals(100, $this->_query->getRows());
    }

    public function testAddField()
    {
        $expectedFields = $this->_query->getFields();
        $expectedFields[] = 'newfield';
        $this->_query->addField('newfield');
        $this->assertEquals($expectedFields, $this->_query->getFields());
    }

    public function testClearFields()
    {
        $this->_query->addField('newfield');
        $this->_query->clearFields();
        $this->assertEquals(array(), $this->_query->getFields());
    }

    public function testAddFields()
    {
        $fields = array('field1','field2');

        $this->_query->clearFields();
        $this->_query->addFields($fields);
        $this->assertEquals($fields, $this->_query->getFields());
    }

    public function testAddFieldsAsStringWithTrim()
    {
        $this->_query->clearFields();
        $this->_query->addFields('field1, field2');
        $this->assertEquals(array('field1','field2'), $this->_query->getFields());
    }

    public function testRemoveField()
    {
        $this->_query->clearFields();
        $this->_query->addFields(array('field1','field2'));
        $this->_query->removeField('field1');
        $this->assertEquals(array('field2'), $this->_query->getFields());
    }

    public function testSetFields()
    {
        $this->_query->clearFields();
        $this->_query->addFields(array('field1','field2'));
        $this->_query->setFields(array('field3','field4'));
        $this->assertEquals(array('field3','field4'), $this->_query->getFields());
    }

    public function testAddSort()
    {
        $this->_query->addSort('field1', Solarium_Query_Select::SORT_DESC);
        $this->assertEquals(
            array('field1' => Solarium_Query_Select::SORT_DESC),
            $this->_query->getSorts()
        );
    }

    public function testAddSorts()
    {
        $sorts = array(
            'field1' => Solarium_Query_Select::SORT_DESC,
            'field2' => Solarium_Query_Select::SORT_ASC
        );

        $this->_query->addSorts($sorts);
        $this->assertEquals(
            $sorts,
            $this->_query->getSorts()
        );
    }

    public function testRemoveSort()
    {
        $sorts = array(
            'field1' => Solarium_Query_Select::SORT_DESC,
            'field2' => Solarium_Query_Select::SORT_ASC
        );

        $this->_query->addSorts($sorts);
        $this->_query->removeSort('field1');
        $this->assertEquals(
            array('field2' => Solarium_Query_Select::SORT_ASC),
            $this->_query->getSorts()
        );
    }

    public function testRemoveInvalidSort()
    {
        $sorts = array(
            'field1' => Solarium_Query_Select::SORT_DESC,
            'field2' => Solarium_Query_Select::SORT_ASC
        );

        $this->_query->addSorts($sorts);
        $this->_query->removeSort('invalidfield'); //continue silently
        $this->assertEquals(
            $sorts,
            $this->_query->getSorts()
        );
    }

    public function testClearSorts()
    {
        $sorts = array(
            'field1' => Solarium_Query_Select::SORT_DESC,
            'field2' => Solarium_Query_Select::SORT_ASC
        );

        $this->_query->addSorts($sorts);
        $this->_query->clearSorts();
        $this->assertEquals(
            array(),
            $this->_query->getSorts()
        );
    }

    public function testSetSorts()
    {
        $sorts = array(
            'field1' => Solarium_Query_Select::SORT_DESC,
            'field2' => Solarium_Query_Select::SORT_ASC
        );

        $this->_query->addSorts($sorts);
        $this->_query->setSorts(array('field3' => Solarium_Query_Select::SORT_ASC));
        $this->assertEquals(
            array('field3' => Solarium_Query_Select::SORT_ASC),
            $this->_query->getSorts()
        );
    }

    public function testAddAndGetFilterQuery()
    {
        $fq = new Solarium_Query_Select_FilterQuery;
        $fq->setKey('fq1')->setQuery('category:1');
        $this->_query->addFilterQuery($fq);

        $this->assertEquals(
            $fq,
            $this->_query->getFilterQuery('fq1')
        );
    }

    public function testAddAndGetFilterQueryWithKey()
    {
        $key = 'fq1';

        $fq = $this->_query->createFilterQuery($key, true);
        $fq->setQuery('category:1');

        $this->assertEquals(
            $key,
            $fq->getKey()
        );

        $this->assertEquals(
            $fq,
            $this->_query->getFilterQuery('fq1')
        );
    }

    public function testAddFilterQueryWithoutKey()
    {
        $fq = new Solarium_Query_Select_FilterQuery;
        $fq->setQuery('category:1');

        $this->setExpectedException('Solarium_Exception');
        $this->_query->addFilterQuery($fq);
    }

    public function testAddFilterQueryWithUsedKey()
    {
        $fq1 = new Solarium_Query_Select_FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_FilterQuery;
        $fq2->setKey('fq1')->setQuery('category:2');

        $this->_query->addFilterQuery($fq1);
        $this->setExpectedException('Solarium_Exception');
        $this->_query->addFilterQuery($fq2);
    }

    public function testGetInvalidFilterQuery()
    {
        $this->assertEquals(
            null,
            $this->_query->getFilterQuery('invalidtag')
        );
    }

    public function testAddFilterQueries()
    {
        $fq1 = new Solarium_Query_Select_FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array('fq1' => $fq1, 'fq2' => $fq2);

        $this->_query->addFilterQueries($filterQueries);
        $this->assertEquals(
            $filterQueries,
            $this->_query->getFilterQueries()
        );
    }

    public function testRemoveFilterQuery()
    {
        $fq1 = new Solarium_Query_Select_FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array($fq1, $fq2);

        $this->_query->addFilterQueries($filterQueries);
        $this->_query->removeFilterQuery('fq1');
        $this->assertEquals(
            array('fq2' => $fq2),
            $this->_query->getFilterQueries()
        );
    }

    public function testRemoveFilterQueryWithObjectInput()
    {
        $fq1 = new Solarium_Query_Select_FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array($fq1, $fq2);

        $this->_query->addFilterQueries($filterQueries);
        $this->_query->removeFilterQuery($fq1);
        $this->assertEquals(
            array('fq2' => $fq2),
            $this->_query->getFilterQueries()
        );
    }

    public function testRemoveInvalidFilterQuery()
    {
        $fq1 = new Solarium_Query_Select_FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array('fq1' => $fq1, 'fq2' => $fq2);

        $this->_query->addFilterQueries($filterQueries);
        $this->_query->removeFilterQuery('fq3'); //continue silently
        $this->assertEquals(
            $filterQueries,
            $this->_query->getFilterQueries()
        );
    }

    public function testClearFilterQueries()
    {
        $fq1 = new Solarium_Query_Select_FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries = array($fq1, $fq2);

        $this->_query->addFilterQueries($filterQueries);
        $this->_query->clearFilterQueries();
        $this->assertEquals(
            array(),
            $this->_query->getFilterQueries()
        );
    }

    public function testSetFilterQueries()
    {
        $fq1 = new Solarium_Query_Select_FilterQuery;
        $fq1->setKey('fq1')->setQuery('category:1');

        $fq2 = new Solarium_Query_Select_FilterQuery;
        $fq2->setKey('fq2')->setQuery('category:2');

        $filterQueries1 = array('fq1' => $fq1, 'fq2' => $fq2);

        $this->_query->addFilterQueries($filterQueries1);

        $fq3 = new Solarium_Query_Select_FilterQuery;
        $fq3->setKey('fq3')->setQuery('category:3');

        $fq4 = new Solarium_Query_Select_FilterQuery;
        $fq4->setKey('fq4')->setQuery('category:4');

        $filterQueries2 = array('fq3' => $fq3, 'fq4' => $fq4);

        $this->_query->setFilterQueries($filterQueries2);

        $this->assertEquals(
            $filterQueries2,
            $this->_query->getFilterQueries()
        );
    }

    public function testConfigMode()
    {
        $config = array(
            'query'  => 'text:mykeyword',
            'sort'   => array('score' => 'asc'),
            'fields' => array('id','title','category'),
            'rows'   => 100,
            'start'  => 200,
            'filterquery' => array(
                array('key' => 'pub', 'tag' => array('pub'),'query' => 'published:true'),
                'online' => array('tag' => 'onl','query' => 'online:true')
            ),
            'component' => array(
                'facetset' => array(
                    'facet' => array(
                        array('type' => 'field', 'key' => 'categories', 'field' => 'category'),
                        'category13' => array('type' => 'query', 'query' => 'category:13')
                    )
                ),
            ),
            'resultclass' => 'MyResultClass',
            'documentclass' => 'MyDocumentClass',
        );
        $query = new Solarium_Query_Select($config);

        $this->assertEquals($config['query'], $query->getQuery());
        $this->assertEquals($config['sort'], $query->getSorts());
        $this->assertEquals($config['fields'], $query->getFields());
        $this->assertEquals($config['rows'], $query->getRows());
        $this->assertEquals($config['start'], $query->getStart());
        $this->assertEquals($config['documentclass'], $query->getDocumentClass());
        $this->assertEquals($config['resultclass'], $query->getResultClass());
        $this->assertEquals('published:true', $query->getFilterQuery('pub')->getQuery());
        $this->assertEquals('online:true', $query->getFilterQuery('online')->getQuery());

        $facets = $query->getFacetSet()->getFacets();
        $this->assertEquals(
            'category',
            $facets['categories']->getField()
        );
        $this->assertEquals(
            'category:13',
            $facets['category13']->getQuery()
        );

        $components = $query->getComponents();
        $this->assertEquals(1, count($components));
        $this->assertThat(array_pop($components), $this->isInstanceOf('Solarium_Query_Select_Component_FacetSet'));
    }

    public function testSetAndGetComponents()
    {
        $mlt = new Solarium_Query_Select_Component_MoreLikeThis;
        $this->_query->setComponent('mlt',$mlt);

        $this->assertEquals(
            array('mlt' => $mlt),
            $this->_query->getComponents()
        );
    }

    public function testSetAndGetComponent()
    {
        $mlt = new Solarium_Query_Select_Component_MoreLikeThis;
        $this->_query->setComponent('mlt',$mlt);

        $this->assertEquals(
            $mlt,
            $this->_query->getComponent('mlt')
        );
    }

    public function testGetInvalidComponent()
    {
        $this->assertEquals(
            null,
            $this->_query->getComponent('invalid')
        );
    }

    public function testGetInvalidComponentAutoload()
    {
        $this->setExpectedException('Solarium_Exception');
        $this->_query->getComponent('invalid', true);
    }

    public function testRemoveComponent()
    {
        $mlt = new Solarium_Query_Select_Component_MoreLikeThis;
        $this->_query->setComponent('mlt',$mlt);

        $this->assertEquals(
            array('mlt' => $mlt),
            $this->_query->getComponents()
        );

        $this->_query->removeComponent('mlt');

        $this->assertEquals(
            array(),
            $this->_query->getComponents()
        );
    }

    public function testRemoveComponentWithObjectInput()
    {
        $mlt = new Solarium_Query_Select_Component_MoreLikeThis;
        $this->_query->setComponent('mlt',$mlt);

        $this->assertEquals(
            array('mlt' => $mlt),
            $this->_query->getComponents()
        );

        $this->_query->removeComponent($mlt);

        $this->assertEquals(
            array(),
            $this->_query->getComponents()
        );
    }

    public function testGetMoreLikeThis()
    {
        $mlt = $this->_query->getMoreLikeThis();

        $this->assertEquals(
            'Solarium_Query_Select_Component_MoreLikeThis',
            get_class($mlt)
        );
    }

    public function testGetDisMax()
    {
        $dismax = $this->_query->getDisMax();

        $this->assertEquals(
            'Solarium_Query_Select_Component_DisMax',
            get_class($dismax)
        );
    }

    public function testGetHighlighting()
    {
        $hlt = $this->_query->getHighlighting();

        $this->assertEquals(
            'Solarium_Query_Select_Component_Highlighting',
            get_class($hlt)
        );
    }

    public function testGetGrouping()
    {
        $grouping = $this->_query->getGrouping();

        $this->assertEquals(
            'Solarium_Query_Select_Component_Grouping',
            get_class($grouping)
        );
    }

    public function testRegisterComponentType()
    {
        $components = $this->_query->getComponentTypes();
        $components['mykey'] = array(
            'component' => 'mycomponent',
            'requestbuilder' => 'mybuilder',
            'responseparser' => 'myparser',
        );

        $this->_query->registerComponentType('mykey','mycomponent','mybuilder','myparser');

        $this->assertEquals(
            $components,
            $this->_query->getComponentTypes()
        );
    }

    public function testCreateFilterQuery()
    {
        $options = array('optionA' => 1, 'optionB' => 2);
        $fq = $this->_query->createFilterQuery($options);

        // check class
       $this->assertThat($fq, $this->isInstanceOf('Solarium_Query_Select_FilterQuery'));

        // check option forwarding
        $fqOptions = $fq->getOptions();
        $this->assertEquals(
            $options['optionB'],
            $fqOptions['optionB']
        );
    }

    public function testGetSpellcheck()
    {
        $spellcheck = $this->_query->getSpellcheck();

        $this->assertEquals(
            'Solarium_Query_Select_Component_Spellcheck',
            get_class($spellcheck)
        );
    }

    public function testGetDistributedSearch()
    {
        $spellcheck = $this->_query->getDistributedSearch();

        $this->assertEquals(
            'Solarium_Query_Select_Component_DistributedSearch',
            get_class($spellcheck)
        );
    }

    public function testGetStats()
    {
        $stats = $this->_query->getStats();

        $this->assertEquals(
            'Solarium_Query_Select_Component_Stats',
            get_class($stats)
        );
    }

    public function testGetDebug()
    {
        $stats = $this->_query->getDebug();

        $this->assertEquals(
            'Solarium_Query_Select_Component_Debug',
            get_class($stats)
        );
    }
}
