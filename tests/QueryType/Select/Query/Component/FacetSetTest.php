<?php

namespace Solarium\Tests\QueryType\Select\Query\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Field;
use Solarium\Component\Facet\JsonAggregation;
use Solarium\Component\Facet\JsonQuery;
use Solarium\Component\Facet\JsonRange;
use Solarium\Component\Facet\JsonTerms;
use Solarium\Component\Facet\MultiQuery;
use Solarium\Component\Facet\Pivot;
use Solarium\Component\Facet\Query as FacetQuery;
use Solarium\Component\Facet\Range;
use Solarium\Component\FacetSet;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\QueryType\Select\Query\Query;

class FacetSetTest extends TestCase
{
    /**
     * @var FacetSet
     */
    protected $facetSet;

    public function setUp(): void
    {
        $this->facetSet = new FacetSet();
    }

    public function testConfigMode()
    {
        $options = [
            'facet' => [
                ['type' => 'query', 'local_key' => 'f1', 'query' => 'category:1'],
                'f2' => ['type' => 'query', 'query' => 'category:2'],
            ],
            'prefix' => 'pr',
            'sort' => 'index',
            'mincount' => 10,
            'missing' => true,
            'extractfromresponse' => true,
            'contains' => 'foobar',
            'containsignorecase' => true,
            'excludeTerms' => 'foo,bar',
            'matches' => '^foo.*',
        ];

        $this->facetSet->setOptions($options);
        $facets = $this->facetSet->getFacets();

        $this->assertCount(2, $facets);
        $this->assertSame($options['prefix'], $this->facetSet->getPrefix());
        $this->assertSame($options['sort'], $this->facetSet->getSort());
        $this->assertSame($options['mincount'], $this->facetSet->getMinCount());
        $this->assertTrue($this->facetSet->getMissing());
        $this->assertTrue($this->facetSet->getExtractFromResponse());
        $this->assertSame($options['contains'], $this->facetSet->getContains());
        $this->assertTrue($this->facetSet->getContainsIgnoreCase());
        $this->assertSame($options['excludeTerms'], $this->facetSet->getExcludeTerms());
        $this->assertSame($options['matches'], $this->facetSet->getMatches());
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMPONENT_FACETSET, $this->facetSet->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\FacetSet',
            $this->facetSet->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\FacetSet',
            $this->facetSet->getRequestBuilder()
        );
    }

    public function testSetAndGetSort()
    {
        $this->facetSet->setSort('index');
        $this->assertSame('index', $this->facetSet->getSort());
    }

    public function testSetAndGetPrefix()
    {
        $this->facetSet->setPrefix('xyz');
        $this->assertSame('xyz', $this->facetSet->getPrefix());
    }

    public function testSetAndGetLimit()
    {
        $this->facetSet->setLimit(12);
        $this->assertSame(12, $this->facetSet->getLimit());
    }

    public function testSetAndGetMinCount()
    {
        $this->facetSet->setMinCount(100);
        $this->assertSame(100, $this->facetSet->getMinCount());
    }

    public function testSetAndGetMissing()
    {
        $this->facetSet->setMissing(true);
        $this->assertTrue($this->facetSet->getMissing());
    }

    public function testSetAndGetContains()
    {
        $this->facetSet->setContains('foobar');
        $this->assertSame('foobar', $this->facetSet->getContains());
    }

    public function testSetAndGetContainsIgnoreCase()
    {
        $this->facetSet->setContainsIgnoreCase(true);
        $this->assertTrue($this->facetSet->getContainsIgnoreCase());
    }

    public function testSetAndGetExcludeTerms()
    {
        $this->facetSet->setExcludeTerms('foo,bar');
        $this->assertSame('foo,bar', $this->facetSet->getExcludeTerms());
    }

    public function testSetAndGetMatches()
    {
        $this->facetSet->setMatches('^foo.*');
        $this->assertSame('^foo.*', $this->facetSet->getMatches());
    }

    public function testAddAndGetFacet()
    {
        $fq = new FacetQuery();
        $fq->setKey('f1')->setQuery('category:1');
        $this->facetSet->addFacet($fq);

        $this->assertSame(
            $fq,
            $this->facetSet->getFacet('f1')
        );
    }

    public function testAddFacetWithoutKey()
    {
        $fq = new FacetQuery();
        $fq->setQuery('category:1');

        $this->expectException(InvalidArgumentException::class);
        $this->facetSet->addFacet($fq);
    }

    public function testAddFacetWithUsedKey()
    {
        $fq1 = new FacetQuery();
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery();
        $fq2->setKey('f1')->setQuery('category:2');

        $this->facetSet->addFacet($fq1);
        $this->expectException(InvalidArgumentException::class);
        $this->facetSet->addFacet($fq2);
    }

    public function testGetInvalidFacet()
    {
        $this->assertNull(
            $this->facetSet->getFacet('invalidtag')
        );
    }

    public function testAddFacets()
    {
        $fq1 = new FacetQuery();
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery();
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = ['f1' => $fq1, 'f2' => $fq2];

        $this->facetSet->addFacets($facets);
        $this->assertSame(
            $facets,
            $this->facetSet->getFacets()
        );
    }

    public function testAddFacetsWithConfig()
    {
        $facets = [
            ['type' => 'query', 'local_key' => 'f1', 'query' => 'category:1'],
            'f2' => ['type' => 'query', 'query' => 'category:2'],
        ];

        $this->facetSet->addFacets($facets);

        $this->assertCount(2, $this->facetSet->getFacets());
    }

    public function testRemoveFacet()
    {
        $fq1 = new FacetQuery();
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery();
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = ['f1' => $fq1, 'f2' => $fq2];

        $this->facetSet->addFacets($facets);
        $this->facetSet->removeFacet('f1');
        $this->assertSame(
            ['f2' => $fq2],
            $this->facetSet->getFacets()
        );
    }

    public function testRemoveFacetWithObjectInput()
    {
        $fq1 = new FacetQuery();
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery();
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = ['f1' => $fq1, 'f2' => $fq2];

        $this->facetSet->addFacets($facets);
        $this->facetSet->removeFacet($fq1);
        $this->assertSame(
            ['f2' => $fq2],
            $this->facetSet->getFacets()
        );
    }

    public function testRemoveInvalidFacet()
    {
        $fq1 = new FacetQuery();
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery();
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = ['f1' => $fq1, 'f2' => $fq2];

        $this->facetSet->addFacets($facets);
        $this->facetSet->removeFacet('f3'); //continue silently
        $this->assertSame(
            $facets,
            $this->facetSet->getFacets()
        );
    }

    public function testClearFacets()
    {
        $fq1 = new FacetQuery();
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery();
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = ['f1' => $fq1, 'f2' => $fq2];

        $this->facetSet->addFacets($facets);
        $this->facetSet->clearFacets();
        $this->assertSame(
            [],
            $this->facetSet->getFacets()
        );
    }

    public function testSetFacets()
    {
        $fq1 = new FacetQuery();
        $fq1->setKey('f1')->setQuery('category:1');

        $fq2 = new FacetQuery();
        $fq2->setKey('f2')->setQuery('category:2');

        $facets = ['f1' => $fq1, 'f2' => $fq2];

        $this->facetSet->addFacets($facets);

        $fq3 = new FacetQuery();
        $fq3->setKey('f3')->setQuery('category:3');

        $fq4 = new FacetQuery();
        $fq4->setKey('f4')->setQuery('category:4');

        $facets = ['f3' => $fq3, 'f4' => $fq4];

        $this->facetSet->setFacets($facets);

        $this->assertSame(
            $facets,
            $this->facetSet->getFacets()
        );
    }

    public function testCreateFacet()
    {
        $type = FacetSet::FACET_FIELD;
        $options = ['optionA' => 1, 'optionB' => 2];
        $facet = $this->facetSet->createFacet($type, $options);

        // check class mapping
        $this->assertSame(
            $type,
            $facet->getType()
        );

        // check option forwarding
        $facetOptions = $facet->getOptions();
        $this->assertSame(
            $options['optionB'],
            $facetOptions['optionB']
        );
    }

    public function testCreateFacetAdd()
    {
        $type = FacetSet::FACET_FIELD;
        $options = ['local_key' => 'mykey', 'optionA' => 1, 'optionB' => 2];
        $facet = $this->facetSet->createFacet($type, $options);

        $this->assertSame($facet, $this->facetSet->getFacet('mykey'));
    }

    public function testCreateFacetAddWithString()
    {
        $type = FacetSet::FACET_FIELD;
        $options = 'mykey';
        $facet = $this->facetSet->createFacet($type, $options);

        $this->assertSame($facet, $this->facetSet->getFacet('mykey'));
    }

    public function testCreateFacetWithInvalidType()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->facetSet->createFacet('invalidtype');
    }

    public function createFacetAddProvider()
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider createFacetAddProvider
     *
     * @param bool $add
     */
    public function testCreateFacetField(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];

        $facetSet = new FacetSet([]);
        $result = $facetSet->createFacetField($options, $add);

        $this->assertInstanceOf(Field::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));
        $this->assertSame('id', $result->getOption('field'));

        if ($add) {
            $this->assertInstanceOf(Field::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetQuery(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];
        $facetSet = new FacetSet([]);
        $result = $facetSet->createFacetQuery($options, $add);

        $this->assertInstanceOf(FacetQuery::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));
        $this->assertSame('*:*', $result->getOption('query'));

        if ($add) {
            $this->assertInstanceOf(FacetQuery::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetMultiQuery(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];
        $facetSet = new FacetSet([]);
        $result = $facetSet->createFacetMultiQuery($options, $add);

        $this->assertInstanceOf(MultiQuery::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));

        if ($add) {
            $this->assertInstanceOf(MultiQuery::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetRange(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];
        $facetSet = new FacetSet([]);
        $result = $facetSet->createFacetRange($options, $add);

        $this->assertInstanceOf(Range::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));

        if ($add) {
            $this->assertInstanceOf(Range::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     */
    public function testCreateFacetPivot(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];
        $facetSet = new FacetSet([]);
        $result = $facetSet->createFacetPivot($options, $add);

        $this->assertInstanceOf(Pivot::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));

        if ($add) {
            $this->assertInstanceOf(Pivot::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     *
     * @param bool $add
     */
    public function testCreateJsonFacetAggregation(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];

        $facetSet = new FacetSet([]);
        $result = $facetSet->createJsonFacetAggregation($options, $add);

        $this->assertInstanceOf(JsonAggregation::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));

        if ($add) {
            $this->assertInstanceOf(JsonAggregation::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     *
     * @param bool $add
     */
    public function testCreateJsonFacetTerms(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];

        $facetSet = new FacetSet([]);
        $result = $facetSet->createJsonFacetTerms($options, $add);

        $this->assertInstanceOf(JsonTerms::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));
        $this->assertSame('id', $result->getOption('field'));

        if ($add) {
            $this->assertInstanceOf(JsonTerms::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     *
     * @param bool $add
     */
    public function testCreateJsonFacetQuery(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];

        $facetSet = new FacetSet([]);
        $result = $facetSet->createJsonFacetQuery($options, $add);

        $this->assertInstanceOf(JsonQuery::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));
        $this->assertSame('*:*', $result->getOption('query'));

        if ($add) {
            $this->assertInstanceOf(JsonQuery::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    /**
     * @dataProvider createFacetAddProvider
     *
     * @param bool $add
     */
    public function testCreateJsonFacetRange(bool $add)
    {
        $options = ['optionA' => 1, 'optionB' => 2, 'local_key' => 'key'];

        $facetSet = new FacetSet([]);
        $result = $facetSet->createJsonFacetRange($options, $add);

        $this->assertInstanceOf(JsonRange::class, $result);
        $this->assertSame(1, $result->getOption('optionA'));
        $this->assertSame(2, $result->getOption('optionB'));

        if ($add) {
            $this->assertInstanceOf(JsonRange::class, $facetSet->getFacet('key'));
        } else {
            $this->assertEmpty($facetSet->getFacet('key'));
        }
    }

    public function testSetAndGetExtractFromResponse()
    {
        $this->facetSet->setExtractFromResponse(true);
        $this->assertTrue($this->facetSet->getExtractFromResponse());
    }
}
