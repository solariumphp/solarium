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

class Solarium_Query_Select_Component_Facet_QueryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Facet_Query
     */
    protected $_facet;

    public function setUp()
    {
        $this->_facet = new Solarium_Query_Select_Component_Facet_Query;
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1','e2'),
            'query' => 'category:1',
        );

        $this->_facet->setOptions($options);

        $this->assertEquals($options['key'], $this->_facet->getKey());
        $this->assertEquals($options['exclude'], $this->_facet->getExcludes());
        $this->assertEquals($options['query'], $this->_facet->getQuery());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Solarium_Query_Select_Component_FacetSet::FACET_QUERY,
            $this->_facet->getType()
        );
    }

    public function testSetAndGetQuery()
    {
        $this->_facet->setQuery('category:1');
        $this->assertEquals('category:1', $this->_facet->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->_facet->setQuery('id:%1%', array(678));
        $this->assertEquals('id:678', $this->_facet->getQuery());
    }
}
