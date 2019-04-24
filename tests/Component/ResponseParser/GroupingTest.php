<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Grouping as Component;
use Solarium\Component\ResponseParser\Grouping as Parser;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\QueryGroup;
use Solarium\Component\Result\Grouping\Result as Result;
use Solarium\QueryType\Select\Query\Query;

class GroupingTest extends TestCase
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

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->grouping = $this->query->getGrouping();
        $this->grouping->addField('fieldA');
        $this->grouping->setFunction('functionF');
        $this->grouping->addQuery('cat:1');

        $data = [
            'grouped' => [
                'fieldA' => [
                    'matches' => 25,
                    'ngroups' => 12,
                    'groups' => [
                        [
                            'groupValue' => 'test value',
                            'doclist' => [
                                'numFound' => 13,
                                'docs' => [
                                    ['id' => 1, 'name' => 'test'],
                                ],
                            ],
                        ],
                    ],
                ],
                'functionF' => [
                    'matches' => 8,
                    'ngroups' => 3,
                    'groups' => [
                        [
                            'groupValue' => true,
                            'doclist' => [
                                'numFound' => 5,
                                'docs' => [
                                    ['id' => 3, 'name' => 'fun'],
                                ],
                            ],
                        ],
                    ],
                ],
                'cat:1' => [
                    'matches' => 40,
                    'doclist' => [
                        'numFound' => 22,
                        'docs' => [
                            ['id' => 2, 'name' => 'dummy2'],
                            ['id' => 5, 'name' => 'dummy5'],
                        ],
                    ],
                ],
            ],
        ];

        $this->result = $this->parser->parse($this->query, $this->grouping, $data);
    }

    public function testGroupParsing()
    {
        $this->assertEquals(3, count($this->result->getGroups()));

        $fieldGroup = $this->result->getGroup('fieldA');
        $queryGroup = $this->result->getGroup('cat:1');
        $functionGroup = $this->result->getGroup('functionF');

        $this->assertInstanceOf(FieldGroup::class, $fieldGroup);
        $this->assertInstanceOf(QueryGroup::class, $queryGroup);
        $this->assertInstanceOf(FieldGroup::class, $functionGroup);
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
        $result = $this->parser->parse($this->query, $this->grouping, []);
        $this->assertEquals([], $result->getGroups());
    }

    public function testParseMissingGroupField()
    {
        //data does not contain 'fieldA'
        $data = [
            'grouped' => [
                'functionF' => [
                    'matches' => 8,
                    'ngroups' => 3,
                    'groups' => [
                        [
                            'groupValue' => true,
                            'doclist' => [
                                'numFound' => 5,
                                'docs' => [
                                    ['id' => 3, 'name' => 'fun'],
                                ],
                            ],
                        ],
                    ],
                ],
                'cat:1' => [
                    'matches' => 40,
                    'doclist' => [
                        'numFound' => 22,
                        'docs' => [
                            ['id' => 2, 'name' => 'dummy2'],
                            ['id' => 5, 'name' => 'dummy5'],
                        ],
                    ],
                ],
            ],
        ];

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
        $data = [
            'grouped' => [
                'fieldA' => [
                    'matches' => 25,
                    'ngroups' => 12,
                    'doclist' => [
                        'numFound' => 13,
                        'docs' => [
                            ['id' => 1, 'name' => 'test'],
                            ['id' => 2, 'name' => 'test2'],
                        ],
                    ],
                ],
            ],
        ];

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
