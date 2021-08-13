<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Select\Query;

use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Configurable;
use Solarium\Core\Query\Helper;
use Solarium\Core\Query\LocalParameters\LocalParametersTrait;

/**
 * Filterquery.
 *
 * @see https://solr.apache.org/guide/common-query-parameters.html#fq-filter-query-parameter
 */
class FilterQuery extends Configurable implements QueryInterface
{
    use LocalParametersTrait;
    use QueryTrait;

    /**
     * Tags for this filterquery.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Query.
     *
     * @var string
     */
    protected $query;

    /**
     * Get key value.
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->getOption('key');
    }

    /**
     * Set key value.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setKey(string $value): self
    {
        $this->setOption('key', $value);

        return $this;
    }

    /**
     * Add a tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function addTag(string $tag): self
    {
        $this->getLocalParameters()->setTag($tag);

        return $this;
    }

    /**
     * Add tags.
     *
     * @param array $tags
     *
     * @return self Provides fluent interface
     */
    public function addTags(array $tags): self
    {
        $this->getLocalParameters()->addTags($tags);

        return $this;
    }

    /**
     * Get all tagss.
     *
     * @return array
     */
    public function getTags(): array
    {
        return $this->getLocalParameters()->getTags();
    }

    /**
     * Remove a tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function removeTag(string $tag): self
    {
        $this->getLocalParameters()->removeTag($tag);

        return $this;
    }

    /**
     * Remove all tags.
     *
     * @return self Provides fluent interface
     */
    public function clearTags(): self
    {
        $this->getLocalParameters()->clearTags();

        return $this;
    }

    /**
     * Set multiple tags.
     *
     * This overwrites any existing tags
     *
     * @param array $tags
     *
     * @return self Provides fluent interface
     */
    public function setTags(array $tags): self
    {
        $this->getLocalParameters()->clearTags()->addTags($tags);

        return $this;
    }

    /**
     * Cache the filter query or not.
     *
     * @param bool $cache
     *
     * @return self Provides fluent interface
     */
    public function setCache(bool $cache): self
    {
        $this->getLocalParameters()->setCache($cache);

        return $this;
    }

    /**
     * Get the information if the filter query should be cached or not.
     *
     * @return bool
     */
    public function getCache(): bool
    {
        $cache = $this->getLocalParameters()->getCache();
        // The default is to cache the filter Query.
        return 'false' !== reset($cache);
    }

    /**
     * Set the cost to cache the filter query.
     *
     * @param int $cost
     *
     * @return self Provides fluent interface
     */
    public function setCost(int $cost): self
    {
        $this->getLocalParameters()->setCost($cost);

        return $this;
    }

    /**
     * Get the cost of the filter query to be cached or not.
     *
     * @return int
     */
    public function getCost(): int
    {
        $cost = $this->getLocalParameters()->getCost();
        // The default cost for filter queries is 0.
        return (int) reset($cost);
    }

    /**
     * Returns a query helper.
     *
     * @return \Solarium\Core\Query\Helper
     */
    public function getHelper(): Helper
    {
        return new Helper();
    }

    /**
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'key':
                    $this->setKey($value);
                    break;
                case 'query':
                    $this->setQuery($value);
                    break;
            }
        }
    }
}
