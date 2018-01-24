<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Field;
use Solarium\Component\FacetSet;
use Solarium\Component\ResponseParser\FacetSet as Parser;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Query\Query;

class FacetSetTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var FacetSet
     */
    protected $facetSet;

    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->parser = new Parser();

        $this->facetSet = new FacetSet();
        $this->facetSet->createFacet('field', array('key' => 'keyA', 'field' => 'fieldA'));
        $this->facetSet->createFacet('query', array('key' => 'keyB'));
        $this->facetSet->createFacet(
            'multiquery',
            array(
                'key' => 'keyC',
                'query' => array(
                    'keyC_A' => array('query' => 'id:1'),
                    'keyC_B' => array('query' => 'id:2'),
                ),
            )
        );
        $this->facetSet->createFacet('range', array('key' => 'keyD'));
        $this->facetSet->createFacet('pivot', array('key' => 'keyE', 'fields' => 'cat,price'));

        $this->query = new Query();
    }

    public function testParse()
    {
        $data = array(
            'facet_counts' => array(
                'facet_fields' => array(
                    'keyA' => array(
                        'value1',
                        12,
                        'value2',
                        3,
                    ),
                ),
                'facet_queries' => array(
                    'keyB' => 23,
                    'keyC_A' => 25,
                    'keyC_B' => 16,
                ),
                'facet_ranges' => array(
                    'keyD' => array(
                        'before' => 3,
                        'after' => 5,
                        'between' => 4,
                        'counts' => array(
                            '1.0',
                            1,
                            '101.0',
                            2,
                            '201.0',
                            1,
                        ),
                    ),
                ),
                'facet_pivot' => array(
                    'keyE' => array(
                        array(
                            'field' => 'cat',
                            'value' => 'abc',
                            'count' => '123',
                            'pivot' => array(
                                array('field' => 'price', 'value' => 1, 'count' => 12),
                                array('field' => 'price', 'value' => 2, 'count' => 8),
                            ),
                        ),
                    ),
                ),
            ),
        );

        $result = $this->parser->parse($this->query, $this->facetSet, $data);
        $facets = $result->getFacets();

        $this->assertEquals(array('keyA', 'keyB', 'keyC', 'keyD', 'keyE'), array_keys($facets));

        $this->assertEquals(
            array('value1' => 12, 'value2' => 3),
            $facets['keyA']->getValues()
        );

        $this->assertEquals(23, $facets['keyB']->getValue());

        $this->assertEquals(
            array('keyC_A' => 25, 'keyC_B' => 16),
            $facets['keyC']->getValues()
        );

        $this->assertEquals(
            array('1.0' => 1, '101.0' => 2, '201.0' => 1),
            $facets['keyD']->getValues()
        );

        $this->assertEquals(3, $facets['keyD']->getBefore());
        $this->assertEquals(4, $facets['keyD']->getBetween());
        $this->assertEquals(5, $facets['keyD']->getAfter());
        $this->assertEquals(1, count($facets['keyE']));

        $this->query = new Query();
    }

    public function testParseExtractFromResponse()
    {
        $data = array(
            'facet_counts' => array(
                'facet_fields' => array(
                    'keyA' => array(
                        'value1',
                        12,
                        'value2',
                        3,
                    ),
                ),
                'facet_queries' => array(
                    'keyB' => 23,
                    'keyC_A' => 25,
                    'keyC_B' => 16,
                ),
                'facet_ranges' => array(
                    'keyD' => array(
                        'before' => 3,
                        'after' => 5,
                        'between' => 4,
                        'counts' => array(
                            '1.0',
                            1,
                            '101.0',
                            2,
                            '201.0',
                            1,
                        ),
                    ),
                ),
                'facet_pivot' => array(
                    'cat,price' => array(
                        array(
                            'field' => 'cat',
                            'value' => 'abc',
                            'count' => '123',
                            'pivot' => array(
                                array('field' => 'price', 'value' => 1, 'count' => 12),
                                array('field' => 'price', 'value' => 2, 'count' => 8),
                            ),
                            'stats' => array(
                                'min' => 4,
                                'max' => 6,
                            ),
                        ),
                    ),
                ),
            ),
        );

        $facetSet = new FacetSet();
        $facetSet->setExtractFromResponse(true);

        $result = $this->parser->parse($this->query, $facetSet, $data);
        $facets = $result->getFacets();

        $this->assertEquals(array('keyA', 'keyB', 'keyC_A', 'keyC_B', 'keyD', 'cat,price'), array_keys($facets));

        $this->assertEquals(
            array('value1' => 12, 'value2' => 3),
            $facets['keyA']->getValues()
        );

        $this->assertEquals(
            23,
            $facets['keyB']->getValue()
        );

        // As the multiquery facet is a Solarium virtual facet type, it cannot be detected based on Solr response data
        $this->assertEquals(
            25,
            $facets['keyC_A']->getValue()
        );

        $this->assertEquals(
            16,
            $facets['keyC_B']->getValue()
        );

        $this->assertEquals(
            array('1.0' => 1, '101.0' => 2, '201.0' => 1),
            $facets['keyD']->getValues()
        );

        $this->assertEquals(
            3,
            $facets['keyD']->getBefore()
        );

        $this->assertEquals(
            4,
            $facets['keyD']->getBetween()
        );

        $this->assertEquals(
            5,
            $facets['keyD']->getAfter()
        );

        $this->assertEquals(
            1,
            count($facets['cat,price'])
        );

        $pivots = $facets['cat,price']->getPivot();

        $this->assertEquals(
            2,
            count($pivots[0]->getStats())
        );

        $this->query = new Query();
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, $this->facetSet, array());
        $this->assertEquals(array(), $result->getFacets());
    }

    public function testInvalidFacetType()
    {
        $facetStub = $this->createMock(Field::class);
        $facetStub->expects($this->once())
             ->method('getType')
             ->will($this->returnValue('invalidfacettype'));
        $facetStub->expects($this->any())
             ->method('getKey')
             ->will($this->returnValue('facetkey'));

        $this->facetSet->addFacet($facetStub);

        $this->expectException(RuntimeException::class);
        $this->parser->parse($this->query, $this->facetSet, array());
    }
}
