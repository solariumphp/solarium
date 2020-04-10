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
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testInitLocalParameters(): void
    {
        $options = [
            'local_key' => 'key',
            'local_exclude' => '',
            'local_tag' => '',
            'local_range' => '',
            'local_stats' => '',
            'local_terms' => '',
            'local_type' => '',
            'local_query' => '',
            'local_query_field' => '',
            'local_default_field' => '',
            'local_max' => '',
            'local_mean' => '',
            'local_min' => '',
            'local_value' => '',
        ];

        $query = new DummyQuery($options);

        $parameters = $query->getLocalParameters();

        $this->assertArrayHasKey(LocalParameter::TYPE_KEY, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_EXCLUDE, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_TAG, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_RANGE, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_STAT, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_TERM, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_TYPE, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_QUERY, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_QUERY_FIELD, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_DEFAULT_FIELD, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_MAX, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_MAX, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_MIN, $parameters);
        $this->assertArrayHasKey(LocalParameter::TYPE_VALUE, $parameters);

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

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testKey(): void
    {
        $parameters = new LocalParameters();
        $parameters->setKey('key');

        $this->assertSame('{!key=key}', $parameters->render());

        $parameters->clearKeys();
        $parameters->addKeys(['key1', 'key2']);

        $this->assertSame('{!key=key1,key2}', $parameters->render());

        $parameters->removeKey('key1');
        $this->assertSame('{!key=key2}', $parameters->render());
        $this->assertSame(['key2'], $parameters->getKeys());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testEmptyValue(): void
    {
        $parameters = new LocalParameters();
        $parameters->setKey('');

        $this->assertNull($parameters->render());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testExclude(): void
    {
        $parameters = new LocalParameters();
        $parameters->setExclude('exclude');

        $this->assertSame('{!ex=exclude}', $parameters->render());

        $parameters->clearExcludes();
        $parameters->addExcludes(['excludeOne', 'excludeTwo']);

        $this->assertSame('{!ex=excludeOne,excludeTwo}', $parameters->render());

        $parameters->removeExclude('excludeOne');
        $this->assertSame('{!ex=excludeTwo}', $parameters->render());
        $this->assertSame(['excludeTwo'], $parameters->getExcludes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceExcludes(): void
    {
        $parameters = new LocalParameters();
        $parameters->setExcludes(['exclude1', 'exclude2']);
        $parameters->setExcludes(['excludeOne', 'excludeTwo']);

        $this->assertSame('{!ex=excludeOne,excludeTwo}', $parameters->render());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testRange(): void
    {
        $parameters = new LocalParameters();
        $parameters->setRange('range');

        $this->assertSame('{!range=range}', $parameters->render());

        $parameters->clearRanges();
        $parameters->addRanges(['rangeOne', 'rangeTwo']);

        $this->assertSame('{!range=rangeOne,rangeTwo}', $parameters->render());

        $parameters->removeRange('rangeOne');
        $this->assertSame('{!range=rangeTwo}', $parameters->render());
        $this->assertSame(['rangeTwo'], $parameters->getRanges());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceRange(): void
    {
        $parameters = new LocalParameters();

        $parameters->setRanges(['range1', 'range2']);
        $parameters->setRanges(['rangeOne', 'rangeTwo']);

        $this->assertSame('{!range=rangeOne,rangeTwo}', $parameters->render());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testTag(): void
    {
        $parameters = new LocalParameters();
        $parameters->setTag('tag');

        $this->assertSame('{!tag=tag}', $parameters->render());

        $parameters->clearTags();
        $parameters->addTags(['tagOne', 'tagTwo']);

        $this->assertSame('{!tag=tagOne,tagTwo}', $parameters->render());

        $parameters->removeTag('tagOne');
        $this->assertSame('{!tag=tagTwo}', $parameters->render());
        $this->assertSame(['tagTwo'], $parameters->getTags());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceTags(): void
    {
        $parameters = new LocalParameters();
        $parameters->setTags(['tag1', 'tag2']);
        $parameters->setTags(['tagOne', 'tagTwo']);

        $this->assertSame('{!tag=tagOne,tagTwo}', $parameters->render());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testTerms(): void
    {
        $parameters = new LocalParameters();
        $parameters->setTerm('terms');

        $this->assertSame('{!terms=terms}', $parameters->render());

        $parameters->clearTerms();
        $parameters->addTerms(['termsOne', 'termsTwo']);

        $this->assertSame('{!terms=termsOne,termsTwo}', $parameters->render());

        $parameters->removeTerms('termsOne');
        $this->assertSame('{!terms=termsTwo}', $parameters->render());
        $this->assertSame(['termsTwo'], $parameters->getTerms());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceRanges(): void
    {
        $parameters = new LocalParameters();

        $parameters->setTerms(['terms1', 'terms2']);
        $parameters->setTerms(['termsOne', 'termsTwo']);

        $this->assertSame('{!terms=termsOne,termsTwo}', $parameters->render());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testQuery(): void
    {
        $parameters = new LocalParameters();
        $parameters->setQuery('query');

        $this->assertSame('{!query=query}', $parameters->render());

        $parameters->clearQueries();
        $parameters->addQueries(['queryOne', 'queryTwo']);

        $this->assertSame('{!query=queryOne,queryTwo}', $parameters->render());

        $parameters->removeQuery('queryOne');
        $this->assertSame('{!query=queryTwo}', $parameters->render());
        $this->assertSame(['queryTwo'], $parameters->getQueries());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testReplaceQueries(): void
    {
        $parameters = new LocalParameters();

        $parameters->setQueries(['query1', 'query2']);
        $parameters->setQueries(['queryOne', 'queryTwo']);

        $this->assertSame('{!query=queryOne,queryTwo}', $parameters->render());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testStats(): void
    {
        $parameters = new LocalParameters();
        $parameters->setStat('stats');

        $this->assertSame('{!stats=stats}', $parameters->render());

        $parameters->clearStats();
        $parameters->addStats(['statsOne', 'statsTwo']);

        $this->assertSame('{!stats=statsOne,statsTwo}', $parameters->render());

        $parameters->removeStat('statsOne');
        $this->assertSame('{!stats=statsTwo}', $parameters->render());
        $this->assertSame(['statsTwo'], $parameters->getStats());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testMin(): void
    {
        $parameters = new LocalParameters();
        $parameters->setMin('min');

        $this->assertSame('{!min=min}', $parameters->render());

        $parameters->clearMin();

        $this->assertEmpty($parameters->getMin());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testMax(): void
    {
        $parameters = new LocalParameters();
        $parameters->setMax('max');

        $this->assertSame('{!max=max}', $parameters->render());

        $parameters->clearMax();

        $this->assertEmpty($parameters->getMax());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testMean(): void
    {
        $parameters = new LocalParameters();
        $parameters->setMean('mean');

        $this->assertSame('{!mean=mean}', $parameters->render());

        $parameters->clearMean();

        $this->assertEmpty($parameters->getMean());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testDefaultField(): void
    {
        $parameters = new LocalParameters();
        $parameters->setDefaultField('defaultField');

        $this->assertSame('{!df=defaultField}', $parameters->render());

        $parameters->clearDefaultFields();
        $parameters->addDefaultFields(['defaultFieldOne', 'defaultFieldTwo']);

        $this->assertSame('{!df=defaultFieldOne,defaultFieldTwo}', $parameters->render());

        $parameters->removeDefaultField('defaultFieldOne');
        $this->assertSame('{!df=defaultFieldTwo}', $parameters->render());
        $this->assertSame(['defaultFieldTwo'], $parameters->getDefaultFields());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testQueryField(): void
    {
        $parameters = new LocalParameters();
        $parameters->setQueryField('queryField');

        $this->assertSame('{!qf=queryField}', $parameters->render());

        $parameters->clearQueryFields();
        $parameters->addQueryFields(['queryFieldOne', 'queryFieldTwo']);

        $this->assertSame('{!qf=queryFieldOne,queryFieldTwo}', $parameters->render());

        $parameters->removeQueryField('queryFieldOne');
        $this->assertSame('{!qf=queryFieldTwo}', $parameters->render());
        $this->assertSame(['queryFieldTwo'], $parameters->getQueryFields());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testType(): void
    {
        $parameters = new LocalParameters();
        $parameters->setType('type');

        $this->assertSame('{!type=type}', $parameters->render());

        $parameters->clearTypes();
        $parameters->addTypes(['typeOne', 'typeTwo']);

        $this->assertSame('{!type=typeOne,typeTwo}', $parameters->render());

        $parameters->removeType('typeOne');
        $this->assertSame('{!type=typeTwo}', $parameters->render());
        $this->assertSame(['typeTwo'], $parameters->getTypes());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \Solarium\Exception\OutOfBoundsException
     */
    public function testLocalValue(): void
    {
        $parameters = new LocalParameters();
        $parameters->setLocalValue('value');

        $this->assertSame('{!v=value}', $parameters->render());

        $parameters->clearLocalValues();
        $parameters->addLocalValues(['valueOne', 'valueTwo']);

        $this->assertSame('{!v=valueOne,valueTwo}', $parameters->render());

        $parameters->removeLocalValue('valueOne');
        $this->assertSame('{!v=valueTwo}', $parameters->render());
        $this->assertSame(['valueTwo'], $parameters->getLocalValues());
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
