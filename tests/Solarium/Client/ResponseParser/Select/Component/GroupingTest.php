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

class Solarium_Client_ResponseParser_Select_Component_GroupingTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Client_ResponseParser_Select_Component_Grouping
     */
    protected $_parser;

    /**
     * @var Solarium_Query_Select
     */
    protected $_query;

    /**
     * @var Solarium_Query_Select_Component_Grouping
     */
    protected $_grouping;

    /**
     * @var Solarium_Result_Select_Grouping
     */
    protected $_result;

    public function setUp()
    {
        $this->_parser = new Solarium_Client_ResponseParser_Select_Component_Grouping;
        $this->_query = new Solarium_Query_Select();
        $this->_grouping = $this->_query->getGrouping();
        $this->_grouping->addField('fieldA');
        $this->_grouping->addQuery('cat:1');

        $data = array(
            'grouped' => array(
                'fieldA' => array(
                    'matches' =>  25,
                    'ngroups' => 12,
                    'groups' => array(
                        array(
                            'groupValue' => 'test value',
                            'doclist' => array(
                                'numFound' => 13,
                                'docs' => array(
                                    array('id' => 1, 'name' => 'test')
                                )
                            )
                        )
                    )
                ),
                'cat:1' => array(
                    'matches' =>  40,
                    'doclist' => array(
                        'numFound' => 22,
                        'docs' => array(
                            array('id' => 2, 'name' => 'dummy2'),
                            array('id' => 5, 'name' => 'dummy5')
                        )
                    )
                )
            )
        );

        $this->_result = $this->_parser->parse($this->_query, $this->_grouping, $data);
    }

    public function testGroupParsing()
    {
        $this->assertEquals(2, count($this->_result->getGroups()));

        $fieldGroup = $this->_result->getGroup('fieldA');
        $queryGroup = $this->_result->getGroup('cat:1');

        $this->assertEquals('Solarium_Result_Select_Grouping_FieldGroup', get_class($fieldGroup));
        $this->assertEquals('Solarium_Result_Select_Grouping_QueryGroup', get_class($queryGroup));
    }

    public function testFieldGroupParsing()
    {
        $fieldGroup = $this->_result->getGroup('fieldA');
        $valueGroups = $fieldGroup->getValueGroups();
        
        $this->assertEquals(25, $fieldGroup->getMatches());
        $this->assertEquals(12, $fieldGroup->getNumberOfGroups());
        $this->assertEquals(1, count($valueGroups));

        $valueGroup = $valueGroups[0];
        $this->assertEquals(13, $valueGroup->getNumFound());

        $docs = $valueGroup->getDocuments();
        $this->assertEquals('test', $docs[0]->name);
    }

    public function testQueryGroupParsing()
    {
        $queryGroup = $this->_result->getGroup('cat:1');

        $this->assertEquals(40, $queryGroup->getMatches());
        $this->assertEquals(22, $queryGroup->getNumFound());

        $docs = $queryGroup->getDocuments();
        $this->assertEquals('dummy5', $docs[1]->name);
    }

    public function testParseNoData()
    {
        $result = $this->_parser->parse($this->_query, $this->_grouping, array());
        $this->assertEquals(array(), $result->getGroups());
    }
}
