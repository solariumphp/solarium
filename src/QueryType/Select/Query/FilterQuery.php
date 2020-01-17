<?php

namespace Solarium\QueryType\Select\Query;

use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Configurable;
use Solarium\Core\Query\Helper;
use Solarium\Core\Query\LocalParameters\LocalParametersTrait;

/**
 * Filterquery.
 *
 * @see http://wiki.apache.org/solr/CommonQueryParameters#fq
 */
class FilterQuery extends Configurable implements QueryInterface
{
    use QueryTrait;
    use LocalParametersTrait;

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
