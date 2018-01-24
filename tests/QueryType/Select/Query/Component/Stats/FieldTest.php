<?php


namespace Solarium\Tests\QueryType\Select\Query\Component\Stats;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Stats\Field;

class FieldTest extends TestCase
{
    /**
     * @var Field
     */
    protected $field;

    public function setUp()
    {
        $this->field = new Field;
    }

    public function testConfigMode()
    {
        $options = array(
            'facet' => 'field1, field2',
            'pivot' => 'piv1'
        );

        $this->field->setOptions($options);
        $this->assertSame(array('field1', 'field2'), $this->field->getFacets());
    }

    public function testSetAndGetKey()
    {
        $this->field->setKey('testkey');
        $this->assertSame('testkey', $this->field->getKey());
    }

    public function testAddFacet()
    {
        $expectedFacets = $this->field->getFacets();
        $expectedFacets[] = 'newfacet';
        $this->field->addFacet('newfacet');
        $this->assertSame($expectedFacets, $this->field->getFacets());
    }

    public function testClearFacets()
    {
        $this->field->addFacet('newfacet');
        $this->field->clearFacets();
        $this->assertSame(array(), $this->field->getFacets());
    }

    public function testAddFacets()
    {
        $facets = array('facet1', 'facet2');

        $this->field->clearFacets();
        $this->field->addFacets($facets);
        $this->assertSame($facets, $this->field->getFacets());
    }

    public function testAddFacetsAsStringWithTrim()
    {
        $this->field->clearFacets();
        $this->field->addFacets('facet1, facet2');
        $this->assertSame(array('facet1', 'facet2'), $this->field->getFacets());
    }

    public function testRemoveFacet()
    {
        $this->field->clearFacets();
        $this->field->addFacets(array('facet1', 'facet2'));
        $this->field->removeFacet('facet1');
        $this->assertSame(array('facet2'), $this->field->getFacets());
    }

    public function testSetFacets()
    {
        $this->field->clearFacets();
        $this->field->addFacets(array('facet1', 'facet2'));
        $this->field->setFacets(array('facet3', 'facet4'));
        $this->assertSame(array('facet3', 'facet4'), $this->field->getFacets());
    }

    public function testAddPivot()
    {
        $expectedPivots = $this->field->getPivots();
        $expectedPivots[] = 'newpivot';
        $this->field->addPivot('newpivot');
        $this->assertSame($expectedPivots, $this->field->getPivots());
    }

    public function testClearPivots()
    {
        $this->field->addPivot('newpivot');
        $this->field->clearPivots();
        $this->assertSame(array(), $this->field->getPivots());
    }

    public function testAddPivots()
    {
        $pivots = array('pivot1', 'pivot2');

        $this->field->clearPivots();
        $this->field->addPivots($pivots);
        $this->assertSame($pivots, $this->field->getPivots());
    }

    public function testAddPivotsAsStringWithTrim()
    {
        $this->field->clearPivots();
        $this->field->addPivots('pivot1, pivot2');
        $this->assertSame(array('pivot1', 'pivot2'), $this->field->getPivots());
    }

    public function testRemovePivot()
    {
        $this->field->clearPivots();
        $this->field->addPivots(array('pivot1', 'pivot2'));
        $this->field->removePivot('pivot1');
        $this->assertSame(array('pivot2'), $this->field->getPivots());
    }

    public function testSetPivots()
    {
        $this->field->clearPivots();
        $this->field->addPivots(array('pivot1', 'pivot2'));
        $this->field->setPivots(array('pivot3', 'pivot4'));
        $this->assertSame(array('pivot3', 'pivot4'), $this->field->getPivots());
    }
}
