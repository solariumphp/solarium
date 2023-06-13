<?php

declare(strict_types=1);

namespace Solarium\Tests\Core\Query\LocalParameters;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Configurable;
use Solarium\Core\Query\LocalParameters\LocalParameter;
use Solarium\Core\Query\LocalParameters\LocalParameters;
use Solarium\Core\Query\LocalParameters\LocalParametersTrait;
use Solarium\Exception\OutOfBoundsException;

/**
 * LocalParametersTest.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class LocalParametersTest extends TestCase
{
    /**
     * @var LocalParameters
     */
    protected $parameters;

    public function setUp(): void
    {
        $this->parameters = new LocalParameters();
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testInitLocalParameters(): void
    {
        $options = [
            'local_key' => 'key',
            'local_exclude' => 'ex1\,ex2,ex3',
            'local_tag' => 'tag1\,tag2,tag3',
            'local_range' => 'r1\,r2,r3',
            'local_stats' => 'stat1\,stat2,stat3',
            'local_terms' => 't1\,t2,t3',
            'local_type' => 'mytype',
            'local_query' => 'myquery',
            'local_query_field' => 'myfield',
            'local_default_field' => 'deffield',
            'local_max' => 'max',
            'local_mean' => 'mean',
            'local_min' => 'min',
            'local_value' => 'value',
            'local_cache' => true,
            'local_cost' => 0,
        ];

        $query = new DummyQuery($options);

        $parameters = $query->getLocalParameters();

        $this->assertArrayHasKey(LocalParameter::TYPE_KEY, $parameters);
        $this->assertSame(['key'], $parameters->getKeys());
        $this->assertArrayHasKey(LocalParameter::TYPE_EXCLUDE, $parameters);
        $this->assertSame(['ex1\,ex2', 'ex3'], $parameters->getExcludes());
        $this->assertArrayHasKey(LocalParameter::TYPE_TAG, $parameters);
        $this->assertSame(['tag1\,tag2', 'tag3'], $parameters->getTags());
        $this->assertArrayHasKey(LocalParameter::TYPE_RANGE, $parameters);
        $this->assertSame(['r1\,r2', 'r3'], $parameters->getRanges());
        $this->assertArrayHasKey(LocalParameter::TYPE_STAT, $parameters);
        $this->assertSame(['stat1\,stat2', 'stat3'], $parameters->getStats());
        $this->assertArrayHasKey(LocalParameter::TYPE_TERM, $parameters);
        $this->assertSame(['t1\,t2', 't3'], $parameters->getTerms());
        $this->assertArrayHasKey(LocalParameter::TYPE_TYPE, $parameters);
        $this->assertSame(['mytype'], $parameters->getTypes());
        $this->assertArrayHasKey(LocalParameter::TYPE_QUERY, $parameters);
        $this->assertSame(['myquery'], $parameters->getQueries());
        $this->assertArrayHasKey(LocalParameter::TYPE_QUERY_FIELD, $parameters);
        $this->assertSame(['myfield'], $parameters->getQueryFields());
        $this->assertArrayHasKey(LocalParameter::TYPE_DEFAULT_FIELD, $parameters);
        $this->assertSame(['deffield'], $parameters->getDefaultFields());
        $this->assertArrayHasKey(LocalParameter::TYPE_MAX, $parameters);
        $this->assertSame(['max'], $parameters->getMax());
        $this->assertArrayHasKey(LocalParameter::TYPE_MEAN, $parameters);
        $this->assertSame(['mean'], $parameters->getMean());
        $this->assertArrayHasKey(LocalParameter::TYPE_MIN, $parameters);
        $this->assertSame(['min'], $parameters->getMin());
        $this->assertArrayHasKey(LocalParameter::TYPE_VALUE, $parameters);
        $this->assertSame(['value'], $parameters->getLocalValues());
        $this->assertArrayHasKey(LocalParameter::TYPE_CACHE, $parameters);
        $this->assertSame(['true'], $parameters->getCache());
        $this->assertArrayHasKey(LocalParameter::TYPE_COST, $parameters);
        $this->assertSame([0], $parameters->getCost());

        $keys = $parameters[LocalParameter::TYPE_KEY];
        $this->assertInstanceOf(LocalParameter::class, $keys);

        unset($parameters[LocalParameter::TYPE_KEY]);
        $this->assertEmpty($parameters->getKeys());
    }

    /**
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testIllegalParameterType(): void
    {
        $query = new DummyQuery([]);
        $parameters = $query->getLocalParameters();
        $parameters[LocalParameter::TYPE_KEY] = 'foo';

        $this->expectException(OutOfBoundsException::class);

        $parameters->getKeys();
    }

    public function testGetUninitedLocalParameters(): void
    {
        $localParameters = (new DummyTraitUse())->getLocalParameters();
        $this->assertEquals(new LocalParameters(), $localParameters);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testKey(): void
    {
        $this->parameters->setKey('key');
        $this->assertSame(['key'], $this->parameters->getKeys());

        $this->parameters->clearKeys();
        $this->assertEmpty($this->parameters->getKeys());

        $this->parameters->addKeys(['key1', 'key2']);
        $this->assertSame(['key1', 'key2'], $this->parameters->getKeys());

        $this->parameters->removeKey('key1');
        $this->assertSame(['key2'], $this->parameters->getKeys());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testExclude(): void
    {
        $this->parameters->setExclude('exclude');
        $this->assertSame(['exclude'], $this->parameters->getExcludes());

        $this->parameters->clearExcludes();
        $this->assertEmpty($this->parameters->getExcludes());

        $this->parameters->addExcludes(['excludeOne', 'excludeTwo']);
        $this->assertSame(['excludeOne', 'excludeTwo'], $this->parameters->getExcludes());

        $this->parameters->removeExclude('excludeOne');
        $this->assertSame(['excludeTwo'], $this->parameters->getExcludes());

        $this->parameters->setExclude('excludeThree');
        $this->assertSame(['excludeTwo', 'excludeThree'], $this->parameters->getExcludes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceExcludes(): void
    {
        $this->parameters->setExcludes(['exclude1', 'exclude2']);
        $this->assertSame(['exclude1', 'exclude2'], $this->parameters->getExcludes());

        $this->parameters->setExcludes(['excludeOne', 'excludeTwo']);
        $this->assertSame(['excludeOne', 'excludeTwo'], $this->parameters->getExcludes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testRange(): void
    {
        $this->parameters->setRange('range');
        $this->assertSame(['range'], $this->parameters->getRanges());

        $this->parameters->clearRanges();
        $this->assertEmpty($this->parameters->getRanges());

        $this->parameters->addRanges(['rangeOne', 'rangeTwo']);
        $this->assertSame(['rangeOne', 'rangeTwo'], $this->parameters->getRanges());

        $this->parameters->removeRange('rangeOne');
        $this->assertSame(['rangeTwo'], $this->parameters->getRanges());

        $this->parameters->setRange('rangeThree');
        $this->assertSame(['rangeTwo', 'rangeThree'], $this->parameters->getRanges());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceRange(): void
    {
        $this->parameters->setRanges(['range1', 'range2']);
        $this->assertSame(['range1', 'range2'], $this->parameters->getRanges());

        $this->parameters->setRanges(['rangeOne', 'rangeTwo']);
        $this->assertSame(['rangeOne', 'rangeTwo'], $this->parameters->getRanges());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testTag(): void
    {
        $this->parameters->setTag('tag');
        $this->assertSame(['tag'], $this->parameters->getTags());

        $this->parameters->clearTags();
        $this->assertEmpty($this->parameters->getTags());

        $this->parameters->addTags(['tagOne', 'tagTwo']);
        $this->assertSame(['tagOne', 'tagTwo'], $this->parameters->getTags());

        $this->parameters->removeTag('tagOne');
        $this->assertSame(['tagTwo'], $this->parameters->getTags());

        $this->parameters->setTag('tagThree');
        $this->assertSame(['tagTwo', 'tagThree'], $this->parameters->getTags());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceTags(): void
    {
        $this->parameters->setTags(['tag1', 'tag2']);
        $this->assertSame(['tag1', 'tag2'], $this->parameters->getTags());

        $this->parameters->setTags(['tagOne', 'tagTwo']);
        $this->assertSame(['tagOne', 'tagTwo'], $this->parameters->getTags());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testTerms(): void
    {
        $this->parameters->setTerm('terms');
        $this->assertSame(['terms'], $this->parameters->getTerms());

        $this->parameters->clearTerms();
        $this->assertEmpty($this->parameters->getTerms());

        $this->parameters->addTerms(['termsOne', 'termsTwo']);
        $this->assertSame(['termsOne', 'termsTwo'], $this->parameters->getTerms());

        $this->parameters->removeTerm('termsOne');
        $this->assertSame(['termsTwo'], $this->parameters->getTerms());

        $this->parameters->setTerm('termsThree');
        $this->assertSame(['termsTwo', 'termsThree'], $this->parameters->getTerms());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceTerms(): void
    {
        $this->parameters->setTerms(['terms1', 'terms2']);
        $this->assertSame(['terms1', 'terms2'], $this->parameters->getTerms());

        $this->parameters->setTerms(['termsOne', 'termsTwo']);
        $this->assertSame(['termsOne', 'termsTwo'], $this->parameters->getTerms());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @deprecated Will be removed in Solarium 8
     */
    public function testRemoveTerms(): void
    {
        $this->parameters->setTerms(['termsOne', 'termsTwo']);
        $this->assertSame(['termsOne', 'termsTwo'], $this->parameters->getTerms());

        $this->parameters->removeTerms('termsOne');
        $this->assertSame(['termsTwo'], $this->parameters->getTerms());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testQuery(): void
    {
        $this->parameters->setQuery('query');
        $this->assertSame(['query'], $this->parameters->getQueries());

        $this->parameters->clearQueries();
        $this->assertEmpty($this->parameters->getQueries());

        $this->parameters->addQueries(['queryOne', 'queryTwo']);
        $this->assertSame(['queryOne', 'queryTwo'], $this->parameters->getQueries());

        $this->parameters->removeQuery('queryOne');
        $this->assertSame(['queryTwo'], $this->parameters->getQueries());

        $this->parameters->setQuery('queryThree');
        $this->assertSame(['queryTwo', 'queryThree'], $this->parameters->getQueries());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceQueries(): void
    {
        $this->parameters->setQueries(['query1', 'query2']);
        $this->assertSame(['query1', 'query2'], $this->parameters->getQueries());

        $this->parameters->setQueries(['queryOne', 'queryTwo']);
        $this->assertSame(['queryOne', 'queryTwo'], $this->parameters->getQueries());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testStats(): void
    {
        $this->parameters->setStat('stats');
        $this->assertSame(['stats'], $this->parameters->getStats());

        $this->parameters->clearStats();
        $this->assertEmpty($this->parameters->getStats());

        $this->parameters->addStats(['statsOne', 'statsTwo']);
        $this->assertSame(['statsOne', 'statsTwo'], $this->parameters->getStats());

        $this->parameters->removeStat('statsOne');
        $this->assertSame(['statsTwo'], $this->parameters->getStats());

        $this->parameters->setStat('statsThree');
        $this->assertSame(['statsTwo', 'statsThree'], $this->parameters->getStats());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceStats(): void
    {
        $this->parameters->setStats(['stats1', 'stats2']);
        $this->assertSame(['stats1', 'stats2'], $this->parameters->getStats());

        $this->parameters->setStats(['statsOne', 'statsTwo']);
        $this->assertSame(['statsOne', 'statsTwo'], $this->parameters->getStats());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testMin(): void
    {
        $this->parameters->setMin('min');
        $this->assertSame(['min'], $this->parameters->getMin());

        $this->parameters->clearMin();
        $this->assertEmpty($this->parameters->getMin());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testMax(): void
    {
        $this->parameters->setMax('max');
        $this->assertSame(['max'], $this->parameters->getMax());

        $this->parameters->clearMax();
        $this->assertEmpty($this->parameters->getMax());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testMean(): void
    {
        $this->parameters->setMean('mean');
        $this->assertSame(['mean'], $this->parameters->getMean());

        $this->parameters->clearMean();
        $this->assertEmpty($this->parameters->getMean());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testDefaultField(): void
    {
        $this->parameters->setDefaultField('defaultField');
        $this->assertSame(['defaultField'], $this->parameters->getDefaultFields());

        $this->parameters->clearDefaultFields();
        $this->assertEmpty($this->parameters->getDefaultFields());

        $this->parameters->addDefaultFields(['defaultFieldOne', 'defaultFieldTwo']);
        $this->assertSame(['defaultFieldOne', 'defaultFieldTwo'], $this->parameters->getDefaultFields());

        $this->parameters->removeDefaultField('defaultFieldOne');
        $this->assertSame(['defaultFieldTwo'], $this->parameters->getDefaultFields());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testQueryField(): void
    {
        $this->parameters->setQueryField('queryField');
        $this->assertSame(['queryField'], $this->parameters->getQueryFields());

        $this->parameters->clearQueryFields();
        $this->assertEmpty($this->parameters->getQueryFields());

        $this->parameters->addQueryFields(['queryFieldOne', 'queryFieldTwo']);
        $this->assertSame(['queryFieldOne', 'queryFieldTwo'], $this->parameters->getQueryFields());

        $this->parameters->removeQueryField('queryFieldOne');
        $this->assertSame(['queryFieldTwo'], $this->parameters->getQueryFields());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testType(): void
    {
        $this->parameters->setType('type');
        $this->assertSame(['type'], $this->parameters->getTypes());

        $this->parameters->clearTypes();
        $this->assertEmpty($this->parameters->getTypes());

        $this->parameters->addTypes(['typeOne', 'typeTwo']);
        $this->assertSame(['typeOne', 'typeTwo'], $this->parameters->getTypes());

        $this->parameters->removeType('typeOne');
        $this->assertSame(['typeTwo'], $this->parameters->getTypes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testLocalValue(): void
    {
        $this->parameters->setLocalValue('value');
        $this->assertSame(['value'], $this->parameters->getLocalValues());

        $this->parameters->clearLocalValues();
        $this->assertEmpty($this->parameters->getLocalValues());

        $this->parameters->addLocalValues(['valueOne', 'valueTwo']);
        $this->assertSame(['valueOne', 'valueTwo'], $this->parameters->getLocalValues());

        $this->parameters->removeLocalValue('valueOne');
        $this->assertSame(['valueTwo'], $this->parameters->getLocalValues());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testCache(): void
    {
        $this->parameters->setCache(true);
        $this->assertSame(['true'], $this->parameters->getCache());

        $this->parameters->setCache(false);
        $this->assertSame(['false'], $this->parameters->getCache());

        $this->parameters->clearCache();
        $this->assertEmpty($this->parameters->getCache());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testCost(): void
    {
        $this->parameters->setCost(1);
        $this->assertSame([1], $this->parameters->getCost());

        $this->parameters->clearCost();
        $this->assertEmpty($this->parameters->getCost());
    }

    public function testGetParameters(): void
    {
        $this->parameters->setKey('key1');
        $this->parameters->setExcludes(['exclude1', 'exclude2']);
        $this->parameters->setCache(true);
        $this->parameters->setCost(1);

        $expected = [
            'key' => ['key1'],
            'ex' => ['exclude1', 'exclude2'],
            'cache' => ['true'],
            'cost' => [1],
        ];
        $this->assertSame($expected, $this->parameters->getParameters());
    }
}

/**
 * Dummy Query.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class DummyQuery extends Configurable
{
    use LocalParametersTrait;
}

class DummyTraitUse
{
    use LocalParametersTrait;

    // trait assumes this is inherited from Configurable
    protected $options = [];
}
