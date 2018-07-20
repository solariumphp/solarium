<?php

namespace Solarium\QueryType\Select\Query;

use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Core\Configurable;
use Solarium\Core\Query\Helper;

/**
 * Filterquery.
 *
 * @see http://wiki.apache.org/solr/CommonQueryParameters#fq
 */
class FilterQuery extends Configurable implements QueryInterface
{
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
     * @return string
     */
    public function getKey()
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
    public function setKey($value)
    {
        return $this->setOption('key', $value);
    }

    /**
     * Add a tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function addTag($tag)
    {
        $this->tags[$tag] = true;

        return $this;
    }

    /**
     * Add tags.
     *
     * @param array $tags
     *
     * @return self Provides fluent interface
     */
    public function addTags($tags)
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        return $this;
    }

    /**
     * Get all tagss.
     *
     * @return array
     */
    public function getTags()
    {
        return array_keys($this->tags);
    }

    /**
     * Remove a tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function removeTag($tag)
    {
        if (isset($this->tags[$tag])) {
            unset($this->tags[$tag]);
        }

        return $this;
    }

    /**
     * Remove all tags.
     *
     * @return self Provides fluent interface
     */
    public function clearTags()
    {
        $this->tags = [];

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
    public function setTags($tags)
    {
        $this->clearTags();

        return $this->addTags($tags);
    }

    /**
     * Returns a query helper.
     *
     * @return \Solarium\Core\Query\Helper
     */
    public function getHelper()
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
                case 'tag':
                    if (!is_array($value)) {
                        $value = [$value];
                    }
                    $this->addTags($value);
                    break;
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
