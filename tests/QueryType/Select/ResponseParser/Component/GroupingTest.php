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

use Solarium\QueryType\Select\Query\Component\Grouping as Component;
use Solarium\QueryType\Select\Result\Grouping\Result as Result;
use Solarium\QueryType\Select\ResponseParser\Component\Grouping as Parser;
use Solarium\QueryType\Select\Query\Query;

class GroupingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Component
     */
    protected $grouping;

    /**
     * @var Result
     */
    protected $result;

    public function setUp()
    {
        $this->parser = new Parser;
        $this->query = new Query();
        $this->grouping = $this->query->getGrouping();
        $this->grouping->addField('fieldA');
        $this->grouping->setFunction('functionF');
        $this->grouping->addQuery('cat:1');

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
                'functionF' => array(
                    'matches' =>  8,
                    'ngroups' => 3,
                    'groups' => array(
                        array(
                            'groupValue' => true,
                            'doclist' => array(
                                'numFound' => 5,
                                'docs' => array(
                                    array('id' => 3, 'name' => 'fun')
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

        $this->result = $this->parser->parse($this->query, $this->grouping, $data);
    }

    public function testGroupParsing()
    {
        $this->assertEquals(3, count($this->result->getGroups()));

        $fieldGroup = $this->result->getGroup('fieldA');
        $queryGroup = $this->result->getGroup('cat:1');
        $functionGroup = $this->result->getGroup('functionF');

        $this->assertEquals('Solarium\QueryType\Select\Result\Grouping\FieldGroup', get_class($fieldGroup));
        $this->assertEquals('Solarium\QueryType\Select\Result\Grouping\QueryGroup', get_class($queryGroup));
        $this->assertEquals('Solarium\QueryType\Select\Result\Grouping\FieldGroup', get_class($functionGroup));
    }

    public function testFieldGroupParsing()
    {
        $fieldGroup = $this->result->getGroup('fieldA');
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
        $queryGroup = $this->result->getGroup('cat:1');

        $this->assertEquals(40, $queryGroup->getMatches());
        $this->assertEquals(22, $queryGroup->getNumFound());

        $docs = $queryGroup->getDocuments();
        $this->assertEquals('dummy5', $docs[1]->name);
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, $this->grouping, array());
        $this->assertEquals(array(), $result->getGroups());
    }

    public function testParseMissingGroupField()
    {
        //data does not contain 'fieldA'
        $data = array(
            'grouped' => array(
                'functionF' => array(
                    'matches' =>  8,
                    'ngroups' => 3,
                    'groups' => array(
                        array(
                            'groupValue' => true,
                            'doclist' => array(
                                'numFound' => 5,
                                'docs' => array(
                                    array('id' => 3, 'name' => 'fun'),
                                ),
                            ),
                        ),
                    ),
                ),
                'cat:1' => array(
                    'matches' =>  40,
                    'doclist' => array(
                        'numFound' => 22,
                        'docs' => array(
                            array('id' => 2, 'name' => 'dummy2'),
                            array('id' => 5, 'name' => 'dummy5'),
                        ),
                    ),
                ),
            ),
        );

        $result = $this->parser->parse($this->query, $this->grouping, $data);
        $this->assertNull($result->getGroup('fieldA'));
    }

    public function testFunctionGroupParsing()
    {
        $fieldGroup = $this->result->getGroup('functionF');
        $valueGroups = $fieldGroup->getValueGroups();

        $this->assertEquals(8, $fieldGroup->getMatches());
        $this->assertEquals(3, $fieldGroup->getNumberOfGroups());
        $this->assertEquals(1, count($valueGroups));

        $valueGroup = $valueGroups[0];
        $this->assertEquals(5, $valueGroup->getNumFound());

        $docs = $valueGroup->getDocuments();
        $this->assertEquals('fun', $docs[0]->name);
    }

    public function testsParseWithSimpleFormat()
    {
        $data = array(
            'grouped' => array(
                'fieldA' => array(
                    'matches' =>  25,
                    'ngroups' => 12,
                    'doclist' => array(
                        'numFound' => 13,
                        'docs' => array(
                            array('id' => 1, 'name' => 'test'),
                            array('id' => 2, 'name' => 'test2'),
                        ),
                    ),
                ),
            ),
        );

        $this->grouping->setFormat(Component::FORMAT_SIMPLE);

        $result = $this->parser->parse($this->query, $this->grouping, $data);

        $fieldGroup = $result->getGroup('fieldA');
        $valueGroups = $fieldGroup->getValueGroups();

        $this->assertEquals(25, $fieldGroup->getMatches());
        $this->assertEquals(12, $fieldGroup->getNumberOfGroups());
        $this->assertEquals(1, count($valueGroups));

        $valueGroup = $valueGroups[0];
        $this->assertEquals(13, $valueGroup->getNumFound());

        $docs = $valueGroup->getDocuments();
        $this->assertEquals('test2', $docs[1]->name);
    }
}
