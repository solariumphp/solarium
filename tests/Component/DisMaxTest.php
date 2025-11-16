<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\DisMax\BoostQuery;
use Solarium\Component\DisMax;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

class DisMaxTest extends TestCase
{
    protected DisMax $disMax;

    public function setUp(): void
    {
        $this->disMax = new DisMax();
    }

    public function testConfigMode(): void
    {
        $options = [
            'queryparser' => 'edismax',
            'queryalternative' => '*:*',
            'queryfields' => 'title^2.0 description',
            'minimummatch' => '2.0',
            'phrasefields' => 'title^2.0 description^3.5',
            'phraseslop' => 2,
            'queryphraseslop' => 4,
            'tie' => 2.1,
            'boostquery' => 'cat:1^3',
            'boostfunctions' => 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2',
        ];

        $this->disMax->setOptions($options);

        $this->assertSame($options['queryparser'], $this->disMax->getQueryParser());
        $this->assertSame($options['queryalternative'], $this->disMax->getQueryAlternative());
        $this->assertSame($options['queryfields'], $this->disMax->getQueryFields());
        $this->assertSame($options['minimummatch'], $this->disMax->getMinimumMatch());
        $this->assertSame($options['phrasefields'], $this->disMax->getPhraseFields());
        $this->assertSame($options['phraseslop'], $this->disMax->getPhraseSlop());
        $this->assertSame($options['queryphraseslop'], $this->disMax->getQueryPhraseSlop());
        $this->assertSame($options['tie'], $this->disMax->getTie());
        $this->assertSame($options['boostquery'], $this->disMax->getBoostQuery());
        $this->assertSame($options['boostfunctions'], $this->disMax->getBoostFunctions());
    }

    public function testGetType(): void
    {
        $this->assertSame(
            Query::COMPONENT_DISMAX,
            $this->disMax->getType()
        );
    }

    public function testGetResponseParser(): void
    {
        $this->assertNull($this->disMax->getResponseParser());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Dismax',
            $this->disMax->getRequestBuilder()
        );
    }

    public function testSetAndGetQueryParser(): void
    {
        $value = 'dummyparser';
        $this->disMax->setQueryParser($value);

        $this->assertSame($value, $this->disMax->getQueryParser());
    }

    public function testSetAndGetQueryAlternative(): void
    {
        $value = '*:*';
        $this->disMax->setQueryAlternative($value);

        $this->assertSame($value, $this->disMax->getQueryAlternative());
    }

    public function testSetAndGetQueryFields(): void
    {
        $value = 'title^2.0 description';
        $this->disMax->setQueryFields($value);

        $this->assertSame($value, $this->disMax->getQueryFields());
    }

    public function testSetAndGetMinimumMatch(): void
    {
        $value = '2.0';
        $this->disMax->setMinimumMatch($value);

        $this->assertSame($value, $this->disMax->getMinimumMatch());
    }

    public function testSetAndGetPhraseFields(): void
    {
        $value = 'title^2.0 description^3.5';
        $this->disMax->setPhraseFields($value);

        $this->assertSame(
            $value,
            $this->disMax->getPhraseFields()
        );
    }

    public function testSetAndGetPhraseSlop(): void
    {
        $value = 2;
        $this->disMax->setPhraseSlop($value);

        $this->assertSame(
            $value,
            $this->disMax->getPhraseSlop()
        );
    }

    public function testSetAndGetQueryPhraseSlop(): void
    {
        $value = 3;
        $this->disMax->setQueryPhraseSlop($value);

        $this->assertSame(
            $value,
            $this->disMax->getQueryPhraseSlop()
        );
    }

    public function testSetAndGetTie(): void
    {
        $value = 2.1;
        $this->disMax->setTie($value);

        $this->assertSame(
            $value,
            $this->disMax->getTie()
        );
    }

    public function testSetAndGetBoostQuery(): void
    {
        $value = 'cat:1^3';
        $this->disMax->setBoostQuery($value);

        $this->assertSame(
            $value,
            $this->disMax->getBoostQuery()
        );
    }

    public function testGetBoostQueryWithNonExistentKey(): void
    {
        $this->assertNull($this->disMax->getBoostQuery('foobar'));
    }

    public function testAddBoostQueryWithArray(): void
    {
        $query = 'cat:1^3';
        $key = 'cat';

        $this->disMax->addBoostQuery(['query' => $query, 'key' => $key]);

        $this->assertSame($query, $this->disMax->getBoostQuery($key));
    }

    public function testAddBoostQueryWithObject(): void
    {
        $query = 'cat:1^3';
        $key = 'cat';

        $bq = new BoostQuery();
        $bq->setKey($key);
        $bq->setQuery($query);

        $this->disMax->addBoostQuery($bq);

        $this->assertSame($query, $this->disMax->getBoostQuery($key));
    }

    public function testAddBoostQueryWithoutKey(): void
    {
        $bq = new BoostQuery();
        $bq->setQuery('category:1');

        $this->expectException(InvalidArgumentException::class);
        $this->disMax->addBoostQuery($bq);
    }

    public function testAddBoostQueryWithEmptyKey(): void
    {
        $bq = new BoostQuery();
        $bq->setKey('')->setQuery('category:1');

        $this->expectException(InvalidArgumentException::class);
        $this->disMax->addBoostQuery($bq);
    }

    public function testAddBoostQueryWithUsedKey(): void
    {
        $bq1 = new BoostQuery();
        $bq1->setKey('bq1')->setQuery('category:1');

        $bq2 = new BoostQuery();
        $bq2->setKey('bq1')->setQuery('category:2');

        $this->disMax->addBoostQuery($bq1);
        $this->expectException(InvalidArgumentException::class);
        $this->disMax->addBoostQuery($bq2);
    }

    public function testAddBoostQueriesWithInnerKeys(): void
    {
        $bqs = [
            ['key' => 'key1', 'query' => 'cat:1'],
            ['key' => 'key2', 'query' => 'cat:2'],
        ];

        $this->disMax->addBoostQueries($bqs);

        $bqs2 = [];

        foreach ($bqs as $bq) {
            $bqs2[$bq['key']] = new BoostQuery($bq);
        }

        $this->assertEquals($bqs2, $this->disMax->getBoostQueries());
    }

    public function testAddBoostQueriesWithOuterKeys(): void
    {
        $bqs = [
            'key1' => ['query' => 'cat:1'],
            'key2' => ['query' => 'cat:2'],
        ];

        $this->disMax->addBoostQueries($bqs);

        $bqs2 = [];

        foreach ($bqs as $key => $bq) {
            $bq['key'] = $key;
            $bqs2[$key] = new BoostQuery($bq);
        }

        $this->assertEquals($bqs2, $this->disMax->getBoostQueries());
    }

    public function testRemoveBoostQueryByKey(): void
    {
        $bqs = [
            'key1' => ['query' => 'cat:1'],
            'key2' => ['query' => 'cat:2'],
        ];

        $this->disMax->addBoostQueries($bqs);
        $this->disMax->removeBoostQuery('key1');

        $this->assertNull($this->disMax->getBoostQuery('key1'));
        $this->assertNotNull($this->disMax->getBoostQuery('key2'));
    }

    public function testRemoveBoostQueryByObject(): void
    {
        $bq1 = new BoostQuery();
        $bq1->setKey('key1')->setQuery('cat:1');

        $bq2 = new BoostQuery();
        $bq2->setKey('key2')->setQuery('cat:2');

        $this->disMax->addBoostQueries([$bq1, $bq2]);
        $this->disMax->removeBoostQuery($bq1);

        $this->assertNull($this->disMax->getBoostQuery('key1'));
        $this->assertNotNull($this->disMax->getBoostQuery('key2'));
    }

    public function testClearBoostQueries(): void
    {
        $bqs = [
            'key1' => ['query' => 'cat:1'],
            'key2' => ['query' => 'cat:2'],
        ];

        $this->disMax->addBoostQueries($bqs);
        $this->disMax->clearBoostQueries();

        $this->assertCount(0, $this->disMax->getBoostQueries());
    }

    public function testSetBoostQueries(): void
    {
        $bqs1 = [
            'key1' => (new BoostQuery())->setKey('key1')->setQuery('cat:1'),
            'key2' => (new BoostQuery())->setKey('key2')->setQuery('cat:2'),
        ];

        $this->disMax->setBoostQueries($bqs1);

        $this->assertSame($bqs1, $this->disMax->getBoostQueries());

        $bqs2 = [
            'key3' => (new BoostQuery())->setKey('key3')->setQuery('cat:3'),
            'key4' => (new BoostQuery())->setKey('key4')->setQuery('cat:4'),
        ];

        $this->disMax->setBoostQueries($bqs2);

        $this->assertSame($bqs2, $this->disMax->getBoostQueries());
    }

    public function testSetAndGetBoostFunctions(): void
    {
        $value = 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2';
        $this->disMax->setBoostFunctions($value);

        $this->assertSame($value, $this->disMax->getBoostFunctions());
    }
}
