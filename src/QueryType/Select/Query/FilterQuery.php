<?php

namespace Solarium\QueryType\Select\Query;

use Solarium\Core\Configurable;
use Solarium\Core\Query\Helper;

/**
 * Filterquery.
 *
 * @see http://wiki.apache.org/solr/CommonQueryParameters#fq
 */
class FilterQuery extends Configurable
{
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
     * Set the query string.
     *
     * This overwrites the current value
     *
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setQuery($query, $bind = null)
    {
        if (null !== $bind) {
            $helper = new Helper();
            $query = $helper->assemble($query, $bind);
        }

        $this->query = trim($query);

        return $this;
    }

    /**
     * Get the query string.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
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
