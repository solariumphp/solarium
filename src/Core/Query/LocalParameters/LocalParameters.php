<?php

declare(strict_types=1);

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query\LocalParameters;

use Solarium\Exception\OutOfBoundsException;

/**
 * Local Parameters.
 *
 * @see https://solr.apache.org/guide/local-parameters-in-queries.html
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class LocalParameters implements \ArrayAccess
{
    /**
     * @var \Solarium\Core\Query\LocalParameters\LocalParameterInterface[]
     */
    private $parameters = [];

    /**
     * @param string $key
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setKey(string $key): self
    {
        return $this->clearKeys()->addValue(LocalParameter::TYPE_KEY, $key);
    }

    /**
     * @param array $keys
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addKeys(array $keys): self
    {
        return $this->addValues(LocalParameter::TYPE_KEY, $keys);
    }

    /**
     * @param string $key
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeKey(string $key): self
    {
        return $this->removeValue(LocalParameter::TYPE_KEY, $key);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearKeys(): self
    {
        return $this->clearValues(LocalParameter::TYPE_KEY);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getKeys(): array
    {
        return $this->getValues(LocalParameter::TYPE_KEY);
    }

    /**
     * @param string $exclude
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setExclude(string $exclude): self
    {
        return $this->addValue(LocalParameter::TYPE_EXCLUDE, $exclude);
    }

    /**
     * @param array $excludes
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setExcludes(array $excludes): self
    {
        return $this->setValues(LocalParameter::TYPE_EXCLUDE, $excludes);
    }

    /**
     * @param array $excludes
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addExcludes(array $excludes): self
    {
        return $this->addValues(LocalParameter::TYPE_EXCLUDE, $excludes);
    }

    /**
     * @param string $exclude
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeExclude(string $exclude): self
    {
        return $this->removeValue(LocalParameter::TYPE_EXCLUDE, $exclude);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearExcludes(): self
    {
        return $this->clearValues(LocalParameter::TYPE_EXCLUDE);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getExcludes(): array
    {
        return $this->getValues(LocalParameter::TYPE_EXCLUDE);
    }

    /**
     * @param string $range
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setRange(string $range): self
    {
        return $this->addValue(LocalParameter::TYPE_RANGE, $range);
    }

    /**
     * @param array $ranges
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setRanges(array $ranges): self
    {
        return $this->setValues(LocalParameter::TYPE_RANGE, $ranges);
    }

    /**
     * @param array $ranges
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addRanges(array $ranges): self
    {
        return $this->addValues(LocalParameter::TYPE_RANGE, $ranges);
    }

    /**
     * @param string $range
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeRange(string $range): self
    {
        return $this->removeValue(LocalParameter::TYPE_RANGE, $range);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearRanges(): self
    {
        return $this->clearValues(LocalParameter::TYPE_RANGE);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getRanges(): array
    {
        return $this->getValues(LocalParameter::TYPE_RANGE);
    }

    /**
     * @param string $tag
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setTag(string $tag): self
    {
        return $this->addValue(LocalParameter::TYPE_TAG, $tag);
    }

    /**
     * @param array $tags
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setTags(array $tags): self
    {
        return $this->setValues(LocalParameter::TYPE_TAG, $tags);
    }

    /**
     * @param array $tags
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addTags(array $tags): self
    {
        return $this->addValues(LocalParameter::TYPE_TAG, $tags);
    }

    /**
     * @param string $tag
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeTag(string $tag): self
    {
        return $this->removeValue(LocalParameter::TYPE_TAG, $tag);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearTags(): self
    {
        return $this->clearValues(LocalParameter::TYPE_TAG);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getTags(): array
    {
        return $this->getValues(LocalParameter::TYPE_TAG);
    }

    /**
     * @param string $term
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setTerm(string $term): self
    {
        return $this->addValue(LocalParameter::TYPE_TERM, $term);
    }

    /**
     * @param array $terms
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setTerms(array $terms): self
    {
        return $this->setValues(LocalParameter::TYPE_TERM, $terms);
    }

    /**
     * @param array $terms
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addTerms(array $terms): self
    {
        return $this->addValues(LocalParameter::TYPE_TERM, $terms);
    }

    /**
     * @param string $terms
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeTerms(string $terms): self
    {
        return $this->removeValue(LocalParameter::TYPE_TERM, $terms);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearTerms(): self
    {
        return $this->clearValues(LocalParameter::TYPE_TERM);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getTerms(): array
    {
        return $this->getValues(LocalParameter::TYPE_TERM);
    }

    /**
     * @param string $query
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setQuery(string $query): self
    {
        return $this->addValue(LocalParameter::TYPE_QUERY, $query);
    }

    /**
     * @param array $queries
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setQueries(array $queries): self
    {
        return $this->setValues(LocalParameter::TYPE_QUERY, $queries);
    }

    /**
     * @param array $queries
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addQueries(array $queries): self
    {
        return $this->addValues(LocalParameter::TYPE_QUERY, $queries);
    }

    /**
     * @param string $query
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeQuery(string $query): self
    {
        return $this->removeValue(LocalParameter::TYPE_QUERY, $query);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearQueries(): self
    {
        return $this->clearValues(LocalParameter::TYPE_QUERY);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getQueries(): array
    {
        return $this->getValues(LocalParameter::TYPE_QUERY);
    }

    /**
     * @param string $stat
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setStat(string $stat): self
    {
        return $this->addValue(LocalParameter::TYPE_STAT, $stat);
    }

    /**
     * @param array $stats
     *
     * @throws \Solarium\Exception\OutOfBoundsException
     *
     * @return $this
     */
    public function setStats(array $stats): self
    {
        return $this->setValues(LocalParameter::TYPE_STAT, $stats);
    }

    /**
     * @param array $stats
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addStats(array $stats): self
    {
        return $this->addValues(LocalParameter::TYPE_STAT, $stats);
    }

    /**
     * @param string $stats
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeStat(string $stats): self
    {
        return $this->removeValue(LocalParameter::TYPE_STAT, $stats);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearStats(): self
    {
        return $this->clearValues(LocalParameter::TYPE_STAT);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this->getValues(LocalParameter::TYPE_STAT);
    }

    /**
     * @param string $min
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setMin(string $min): self
    {
        return $this->addValue(LocalParameter::TYPE_MIN, $min);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearMin(): self
    {
        return $this->clearValues(LocalParameter::TYPE_MIN);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getMin(): array
    {
        return $this->getValues(LocalParameter::TYPE_MIN);
    }

    /**
     * @param string $max
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setMax(string $max): self
    {
        return $this->addValue(LocalParameter::TYPE_MAX, $max);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearMax(): self
    {
        return $this->clearValues(LocalParameter::TYPE_MAX);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getMax(): array
    {
        return $this->getValues(LocalParameter::TYPE_MAX);
    }

    /**
     * @param string $mean
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setMean(string $mean): self
    {
        return $this->addValue(LocalParameter::TYPE_MEAN, $mean);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearMean(): self
    {
        return $this->clearValues(LocalParameter::TYPE_MEAN);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getMean(): array
    {
        return $this->getValues(LocalParameter::TYPE_MEAN);
    }

    /**
     * @param string $defaultField
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setDefaultField(string $defaultField): self
    {
        return $this->addValue(LocalParameter::TYPE_DEFAULT_FIELD, $defaultField);
    }

    /**
     * @param array $defaultFields
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addDefaultFields(array $defaultFields): self
    {
        return $this->addValues(LocalParameter::TYPE_DEFAULT_FIELD, $defaultFields);
    }

    /**
     * @param string $defaultField
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeDefaultField(string $defaultField): self
    {
        return $this->removeValue(LocalParameter::TYPE_DEFAULT_FIELD, $defaultField);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearDefaultFields(): self
    {
        return $this->clearValues(LocalParameter::TYPE_DEFAULT_FIELD);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getDefaultFields(): array
    {
        return $this->getValues(LocalParameter::TYPE_DEFAULT_FIELD);
    }

    /**
     * @param string $queryField
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setQueryField(string $queryField): self
    {
        return $this->addValue(LocalParameter::TYPE_QUERY_FIELD, $queryField);
    }

    /**
     * @param array $queryFields
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addQueryFields(array $queryFields): self
    {
        return $this->addValues(LocalParameter::TYPE_QUERY_FIELD, $queryFields);
    }

    /**
     * @param string $queryField
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeQueryField(string $queryField): self
    {
        return $this->removeValue(LocalParameter::TYPE_QUERY_FIELD, $queryField);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearQueryFields(): self
    {
        return $this->clearValues(LocalParameter::TYPE_QUERY_FIELD);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getQueryFields(): array
    {
        return $this->getValues(LocalParameter::TYPE_QUERY_FIELD);
    }

    /**
     * @param string $type
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setType(string $type): self
    {
        return $this->addValue(LocalParameter::TYPE_TYPE, $type);
    }

    /**
     * @param array $types
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addTypes(array $types): self
    {
        return $this->addValues(LocalParameter::TYPE_TYPE, $types);
    }

    /**
     * @param string $type
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeType(string $type): self
    {
        return $this->removeValue(LocalParameter::TYPE_TYPE, $type);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearTypes(): self
    {
        return $this->clearValues(LocalParameter::TYPE_TYPE);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getTypes(): array
    {
        return $this->getValues(LocalParameter::TYPE_TYPE);
    }

    /**
     * @param string $value
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setLocalValue(string $value): self
    {
        return $this->addValue(LocalParameter::TYPE_VALUE, $value);
    }

    /**
     * @param array $values
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function addLocalValues(array $values): self
    {
        return $this->addValues(LocalParameter::TYPE_VALUE, $values);
    }

    /**
     * @param string $value
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeLocalValue(string $value): self
    {
        return $this->removeValue(LocalParameter::TYPE_VALUE, $value);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearLocalValues(): self
    {
        return $this->clearValues(LocalParameter::TYPE_VALUE);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getLocalValues(): array
    {
        return $this->getValues(LocalParameter::TYPE_VALUE);
    }

    /**
     * @param bool $cache
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setCache(bool $cache): self
    {
        return $this->clearCache()->addValue(LocalParameter::TYPE_CACHE, $cache ? 'true' : 'false');
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearCache(): self
    {
        return $this->clearValues(LocalParameter::TYPE_CACHE);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getCache(): array
    {
        return $this->getValues(LocalParameter::TYPE_CACHE);
    }

    /**
     * @param int $cost
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setCost(int $cost): self
    {
        return $this->clearCost()->addValue(LocalParameter::TYPE_COST, $cost);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function clearCost(): self
    {
        return $this->clearValues(LocalParameter::TYPE_COST);
    }

    /**
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getCost(): array
    {
        return $this->getValues(LocalParameter::TYPE_COST);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->parameters[$offset]);
    }

    #[\ReturnTypeWillChange]
    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->parameters[$offset] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->parameters[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->parameters[$offset]);
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    private function addValue(string $type, $value): self
    {
        $this->getParameter($type)->addValue($value);

        return $this;
    }

    /**
     * @param string $type
     * @param array  $values
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    private function addValues(string $type, array $values): self
    {
        $this->getParameter($type)->addValues($values);

        return $this;
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    private function removeValue(string $type, $value): self
    {
        $this->getParameter($type)->removeValue($value);

        return $this;
    }

    /**
     * @param string $type
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    private function clearValues(string $type): self
    {
        $this->getParameter($type)->clearValues();

        return $this;
    }

    /**
     * @param string $type
     *
     * @throws OutOfBoundsException
     *
     * @return array
     */
    private function getValues(string $type): array
    {
        return $this->getParameter($type)->getValues();
    }

    /**
     * @param string $type
     * @param array  $values
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    private function setValues(string $type, array $values): self
    {
        $this->getParameter($type)->setValues($values);

        return $this;
    }

    /**
     * @param string $type
     *
     * @throws OutOfBoundsException
     *
     * @return \Solarium\Core\Query\LocalParameters\LocalParameterInterface
     */
    private function getParameter(string $type): LocalParameterInterface
    {
        if (false === isset($this->parameters[$type])) {
            $this->parameters[$type] = new LocalParameter($type);
        }

        if (!$this->parameters[$type] instanceof LocalParameterInterface) {
            throw new OutOfBoundsException(sprintf('Parameter defined for %s is no %s', $type, LocalParameterInterface::class));
        }

        return $this->parameters[$type];
    }

    /**
     * Get all local parameters in a key => value format.
     *
     * @return array
     */
    public function getParameters(): array
    {
        $params = [];

        /** @var LocalParameterInterface $parameter */
        foreach ($this->parameters as $parameter) {
            $params[$parameter->getType()] = $parameter->getValues();
        }

        return $params;
    }
}
