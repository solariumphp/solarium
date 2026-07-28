<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Grouping;
use Solarium\Component\ResponseParser\Grouping as Parser;
use Solarium\Component\Result\Grouping\FieldGroup;
use Solarium\Component\Result\Grouping\QueryGroup;
use Solarium\Component\Result\Grouping\Result;
use Solarium\QueryType\Select\Query\Query;

class GroupingTest extends TestCase
{
    protected Parser $parser;

    protected Query $query;

    protected Grouping $grouping;

    protected Result $result;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->grouping = $this->query->getGrouping();
        $this->grouping->addField('fieldA');
        $this->grouping->addField('fieldB');
        $this->grouping->addField('fieldC');
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
                                'start' => 0,
                                'docs' => [
                                    ['id' => 1, 'name' => 'test'],
                                ],
                            ],
                        ],
                    ],
                ],
                'fieldB' => [
                    'matches' => 25,
                    'ngroups' => 2,
                    'groups' => [
                        [
                            'groupValue' => 3.14,
                            'doclist' => [
                                'numFound' => 1,
                                'start' => 0,
                                'docs' => [
                                    ['id' => 2, 'name' => 'float test'],
                                ],
                            ],
                        ],
                        [
                            'groupValue' => 42,
                            'doclist' => [
                                'numFound' => 1,
                                'start' => 0,
                                'docs' => [
                                    ['id' => 3, 'name' => 'int test'],
                                ],
                            ],
                        ],
                    ],
                ],
                'fieldC' => [
                    'matches' => 25,
                    'ngroups' => 3,
                    'groups' => [
                        [
                            'groupValue' => true,
                            'doclist' => [
                                'numFound' => 1,
                                'start' => 0,
                                'docs' => [
                                    ['id' => 1, 'name' => 'true test'],
                                ],
                            ],
                        ],
                        [
                            'groupValue' => false,
                            'doclist' => [
                                'numFound' => 1,
                                'start' => 0,
                                'docs' => [
                                    ['id' => 2, 'name' => 'false test'],
                                ],
                            ],
                        ],
                        [
                            'groupValue' => null,
                            'doclist' => [
                                'numFound' => 1,
                                'start' => 0,
                                'docs' => [
                                    ['id' => 3, 'name' => 'null test'],
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
                                'start' => 0,
                                'maxScore' => 0.97027725,
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
                        'start' => 0,
                        'maxScore' => 0.97027725,
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

    public function testGroupParsing(): void
    {
        $this->assertCount(5, $this->result->getGroups());

        $fieldGroup = $this->result->getGroup('fieldA');
        $queryGroup = $this->result->getGroup('cat:1');
        $functionGroup = $this->result->getGroup('functionF');

        $this->assertInstanceOf(FieldGroup::class, $fieldGroup);
        $this->assertInstanceOf(QueryGroup::class, $queryGroup);
        $this->assertInstanceOf(FieldGroup::class, $functionGroup);
    }

    public function testFieldGroupParsing(): void
    {
        $fieldGroup = $this->result->getGroup('fieldA');
        $valueGroups = $fieldGroup->getValueGroups();

        $this->assertEquals(25, $fieldGroup->getMatches());
        $this->assertEquals(12, $fieldGroup->getNumberOfGroups());
        $this->assertCount(1, $valueGroups);

        $valueGroup = $valueGroups[0];
        $this->assertEquals('test value', $valueGroup->getValue());
        $this->assertEquals(13, $valueGroup->getNumFound());
        $this->assertEquals(0, $valueGroup->getStart());
        $this->assertNull($valueGroup->getMaximumScore());

        $docs = $valueGroup->getDocuments();
        $this->assertEquals('test', $docs[0]->name);

        $fieldGroup = $this->result->getGroup('fieldB');
        $valueGroups = $fieldGroup->getValueGroups();

        $this->assertCount(2, $valueGroups);
        $this->assertEquals(3.14, $valueGroups[0]->getValue());
        $this->assertIsInt($valueGroups[1]->getValue());
        $this->assertEquals(42, $valueGroups[1]->getValue());

        $fieldGroup = $this->result->getGroup('fieldC');
        $valueGroups = $fieldGroup->getValueGroups();

        $this->assertCount(3, $valueGroups);
        $this->assertTrue($valueGroups[0]->getValue());
        $this->assertFalse($valueGroups[1]->getValue());
        $this->assertNull($valueGroups[2]->getValue());
    }

    public function testQueryGroupParsing(): void
    {
        $queryGroup = $this->result->getGroup('cat:1');

        $this->assertEquals(40, $queryGroup->getMatches());
        $this->assertEquals(22, $queryGroup->getNumFound());
        $this->assertEquals(0, $queryGroup->getStart());
        $this->assertEquals(0.97027725, $queryGroup->getMaximumScore());

        $docs = $queryGroup->getDocuments();
        $this->assertEquals('dummy5', $docs[1]->name);
    }

    /**
     * Test fix for maxScore being returned as "NaN" when group.query doesn't match any docs.
     *
     * @see https://issues.apache.org/jira/browse/SOLR-13839
     */
    public function testQueryGroupParsingFixForSolr13839(): void
    {
        $data = [
            'grouped' => [
                'cat:1' => [
                    'matches' => 40,
                    'doclist' => [
                        'numFound' => 0,
                        'start' => 0,
                        'maxScore' => 'NaN',
                        'docs' => [],
                    ],
                ],
            ],
        ];

        $result = $this->parser->parse($this->query, $this->grouping, $data);
        $queryGroup = $result->getGroup('cat:1');

        $this->assertEquals(40, $queryGroup->getMatches());
        $this->assertEquals(0, $queryGroup->getNumFound());
        $this->assertEquals(0, $queryGroup->getStart());
        $this->assertNull($queryGroup->getMaximumScore());
        $this->assertEquals([], $queryGroup->getDocuments());
    }

    public function testParseNoData(): void
    {
        $result = $this->parser->parse($this->query, $this->grouping, []);

        $this->assertEquals([], $result->getGroups());
    }

    public function testParseMissingGroupField(): void
    {
        // data does not contain 'fieldA'
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

    public function testFunctionGroupParsing(): void
    {
        $fieldGroup = $this->result->getGroup('functionF');
        $valueGroups = $fieldGroup->getValueGroups();

        $this->assertEquals(8, $fieldGroup->getMatches());
        $this->assertEquals(3, $fieldGroup->getNumberOfGroups());
        $this->assertCount(1, $valueGroups);

        $valueGroup = $valueGroups[0];
        $this->assertTrue($valueGroup->getValue());
        $this->assertEquals(5, $valueGroup->getNumFound());
        $this->assertEquals(0, $valueGroup->getStart());
        $this->assertEquals(0.97027725, $valueGroup->getMaximumScore());

        $docs = $valueGroup->getDocuments();
        $this->assertEquals('fun', $docs[0]->name);
    }

    public function testsParseWithSimpleFormat(): void
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

        $this->grouping->setFormat(Grouping::FORMAT_SIMPLE);

        $result = $this->parser->parse($this->query, $this->grouping, $data);

        $fieldGroup = $result->getGroup('fieldA');
        $valueGroups = $fieldGroup->getValueGroups();

        $this->assertEquals(25, $fieldGroup->getMatches());
        $this->assertEquals(12, $fieldGroup->getNumberOfGroups());
        $this->assertCount(1, $valueGroups);

        $valueGroup = $valueGroups[0];
        $this->assertNull($valueGroup->getValue());
        $this->assertEquals(13, $valueGroup->getNumFound());

        $docs = $valueGroup->getDocuments();
        $this->assertEquals('test2', $docs[1]->name);
    }
}
