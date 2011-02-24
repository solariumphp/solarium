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

class Solarium_Client_Request_SelectTest extends PHPUnit_Framework_TestCase
{

    protected $_query;

    protected $_options = array(
        'host' => '127.0.0.1',
        'port' => 80,
        'path' => '/solr',
        'core' => null,
    );

    public function setUp()
    {
        $this->_query = new Solarium_Query_Select;
    }

    public function testSelectUrlWithDefaultValues()
    {
        $request = new Solarium_Client_Request_Select($this->_options, $this->_query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            'http://127.0.0.1:80/solr/select?q=*:*&start=0&rows=10&fl=*,score&wt=json',
            urldecode($request->getUrl())
        );
    }

    public function testSelectUrlWithSort()
    {
        $this->_query->addSortField('id', Solarium_Query_Select::SORT_ASC);
        $this->_query->addSortField('name', Solarium_Query_Select::SORT_DESC);
        $request = new Solarium_Client_Request_Select($this->_options, $this->_query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            'http://127.0.0.1:80/solr/select?q=*:*&start=0&rows=10&fl=*,score&wt=json&sort=id asc,name desc',
            urldecode($request->getUrl())
        );
    }

    public function testSelectUrlWithSortAndFilters()
    {
        $this->_query->addSortField('id', Solarium_Query_Select::SORT_ASC);
        $this->_query->addSortField('name', Solarium_Query_Select::SORT_DESC);
        $this->_query->addFilterQuery(new Solarium_Query_Select_FilterQuery(array('key' => 'f1', 'query' => 'published:true')));
        $this->_query->addFilterQuery(new Solarium_Query_Select_FilterQuery(array('key' => 'f2', 'tag' => array('t1','t2'), 'query' => 'category:23')));
        $request = new Solarium_Client_Request_Select($this->_options, $this->_query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            'http://127.0.0.1:80/solr/select?q=*:*&start=0&rows=10&fl=*,score&wt=json&sort=id asc,name desc&fq=published:true&fq={!tag=t1,t2}category:23',
            urldecode($request->getUrl())
        );
    }

    public function testSelectUrlWithFacets()
    {
        $this->_query->addFacet(new Solarium_Query_Select_Facet_Field(array('key' => 'f1', 'field' => 'owner')));
        $this->_query->addFacet(new Solarium_Query_Select_Facet_Query(array('key' => 'f2', 'query' => 'category:23')));
        $request = new Solarium_Client_Request_Select($this->_options, $this->_query);

        $this->assertEquals(
            null,
            $request->getRawData()
        );

        $this->assertEquals(
            'http://127.0.0.1:80/solr/select?q=*:*&start=0&rows=10&fl=*,score&wt=json&facet=true&facet.field={!key=f1}owner&facet.query={!key=f2}category:23',
            urldecode($request->getUrl())
        );
    }

    public function testUnknownFacetType()
    {
        $this->_query->addFacet(new UnknownFacet(array('key' => 'f1', 'field' => 'owner')));
        $this->setExpectedException('Solarium_Exception');
        new Solarium_Client_Request_Select($this->_options, $this->_query);
    }
}

class UnknownFacet extends Solarium_Query_Select_Facet_Field{

    public function getType()
    {
        return 'unknown';
    }


}