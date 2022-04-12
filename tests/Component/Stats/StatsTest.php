<?php

namespace Solarium\Tests\Component\Stats;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Stats\Field;
use Solarium\Component\Stats\Stats;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

class StatsTest extends TestCase
{
    /**
     * @var Stats
     */
    protected $stats;

    public function setUp(): void
    {
        $this->stats = new Stats();
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMPONENT_STATS, $this->stats->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Stats',
            $this->stats->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Stats',
            $this->stats->getRequestBuilder()
        );
    }

    public function testConfigMode()
    {
        $options = [
            'facet' => 'field1, field2',
            'field' => [
                'f1' => [],
                'f2' => [],
            ],
        ];

        $this->stats->setOptions($options);

        $this->assertSame(['field1', 'field2'], $this->stats->getFacets());
        $this->assertSame(['f1', 'f2'], array_keys($this->stats->getFields()));
    }

    public function testCreateFieldWithKey()
    {
        $field = $this->stats->createField('mykey');

        // check class
        $this->assertThat($field, $this->isInstanceOf('Solarium\Component\Stats\Field'));

        $this->assertSame(
            'mykey',
            $field->getKey()
        );
    }

    public function testCreateFieldWithOptions()
    {
        $options = ['key' => 'testkey'];
        $field = $this->stats->createField($options);

        // check class
        $this->assertThat($field, $this->isInstanceOf('Solarium\Component\Stats\Field'));

        // check option forwarding
        $fieldOptions = $field->getOptions();
        $this->assertSame(
            $options['key'],
            $field->getKey()
        );
    }

    public function testAddAndGetField()
    {
        $field = new Field();
        $field->setKey('f1');
        $this->stats->addField($field);

        $this->assertSame(
            $field,
            $this->stats->getField('f1')
        );
    }

    public function testAddFieldWithOptions()
    {
        $this->stats->addField(['key' => 'f1']);

        $this->assertSame(
            'f1',
            $this->stats->getField('f1')->getKey()
        );
    }

    public function testAddAndGetFieldWithKey()
    {
        $key = 'f1';

        $fld = $this->stats->createField($key);

        $this->assertSame(
            $key,
            $fld->getKey()
        );

        $this->assertSame(
            $fld,
            $this->stats->getField('f1')
        );
    }

    public function testAddFieldWithoutKey()
    {
        $fld = new Field();

        $this->expectException(InvalidArgumentException::class);
        $this->stats->addField($fld);
    }

    public function testAddFieldWithEmptyKey()
    {
        $fld = new Field();
        $fld->setKey('');

        $this->expectException(InvalidArgumentException::class);
        $this->stats->addField($fld);
    }

    public function testAddFieldWithUsedKey()
    {
        $f1 = new Field();
        $f1->setKey('f1');

        $f2 = new Field();
        $f2->setKey('f1');

        $this->stats->addField($f1);
        $this->expectException(InvalidArgumentException::class);
        $this->stats->addField($f2);
    }

    public function testGetInvalidField()
    {
        $this->assertNull(
            $this->stats->getField('invalidkey')
        );
    }

    public function testAddFields()
    {
        $f1 = new Field();
        $f1->setKey('f1');

        $f2 = new Field();
        $f2->setKey('f2');

        $fields = ['f1' => $f1, 'f2' => $f2];

        $this->stats->addFields($fields);
        $this->assertSame(
            $fields,
            $this->stats->getFields()
        );
    }

    public function testAddFieldsWithOptions()
    {
        $fields = [
            'f1' => [''],
            ['key' => 'f2'],
        ];

        $this->stats->addFields($fields);
        $fields = $this->stats->getFields();

        $this->assertSame(['f1', 'f2'], array_keys($fields));
    }

    public function testRemoveField()
    {
        $f1 = new Field();
        $f1->setKey('f1');

        $f2 = new Field();
        $f2->setKey('f2');

        $fields = ['f1' => $f1, 'f2' => $f2];

        $this->stats->addFields($fields);
        $this->stats->removeField('f1');
        $this->assertSame(
            ['f2' => $f2],
            $this->stats->getFields()
        );
    }

    public function testRemoveFieldWithObjectInput()
    {
        $f1 = new Field();
        $f1->setKey('f1');

        $f2 = new Field();
        $f2->setKey('f2');

        $fields = [$f1, $f2];

        $this->stats->addFields($fields);
        $this->stats->removeField($f1);
        $this->assertSame(
            ['f2' => $f2],
            $this->stats->getFields()
        );
    }

    public function testRemoveInvalidField()
    {
        $f1 = new Field();
        $f1->setKey('f1');

        $f2 = new Field();
        $f2->setKey('f2');

        $fields = ['f1' => $f1, 'f2' => $f2];

        $this->stats->addFields($fields);
        $this->stats->removeField('f3'); // continue silently
        $this->assertSame(
            $fields,
            $this->stats->getFields()
        );
    }

    public function testClearFields()
    {
        $f1 = new Field();
        $f1->setKey('f1');

        $f2 = new Field();
        $f2->setKey('f2');

        $fields = [$f1, $f2];

        $this->stats->addFields($fields);
        $this->stats->clearFields();
        $this->assertSame(
            [],
            $this->stats->getFields()
        );
    }

    public function testSetFields()
    {
        $f1 = new Field();
        $f1->setKey('f1');

        $f2 = new Field();
        $f2->setKey('f2');

        $fields = [$f1, $f2];

        $this->stats->addFields($fields);

        $f3 = new Field();
        $f3->setKey('f3');

        $f4 = new Field();
        $f4->setKey('f4');

        $fields2 = ['f3' => $f3, 'f4' => $f4];

        $this->stats->setFields($fields2);

        $this->assertSame(
            $fields2,
            $this->stats->getFields()
        );
    }

    public function testAddFacet()
    {
        $expectedFacets = $this->stats->getFacets();
        $expectedFacets[] = 'newfacet';
        $this->stats->addFacet('newfacet');
        $this->assertSame($expectedFacets, $this->stats->getFacets());
    }

    public function testClearFacets()
    {
        $this->stats->addFacet('newfacet');
        $this->stats->clearFacets();
        $this->assertSame([], $this->stats->getFacets());
    }

    public function testAddFacets()
    {
        $facets = ['facet1', 'facet2'];

        $this->stats->clearFacets();
        $this->stats->addFacets($facets);
        $this->assertSame($facets, $this->stats->getFacets());
    }

    public function testAddFacetsAsStringWithTrim()
    {
        $this->stats->clearFacets();
        $this->stats->addFacets('facet1, facet2');
        $this->assertSame(['facet1', 'facet2'], $this->stats->getFacets());
    }

    public function testRemoveFacet()
    {
        $this->stats->clearFacets();
        $this->stats->addFacets(['facet1', 'facet2']);
        $this->stats->removeFacet('facet1');
        $this->assertSame(['facet2'], $this->stats->getFacets());
    }

    public function testSetFacets()
    {
        $this->stats->clearFacets();
        $this->stats->addFacets(['facet1', 'facet2']);
        $this->stats->setFacets(['facet3', 'facet4']);
        $this->assertSame(['facet3', 'facet4'], $this->stats->getFacets());
    }
}
