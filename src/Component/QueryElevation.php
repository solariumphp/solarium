<?php

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\QueryElevation as RequestBuilder;

/**
 * QueryElevation component.
 *
 * @see https://lucene.apache.org/solr/guide/the-query-elevation-component.html
 */
class QueryElevation extends AbstractComponent
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'transformers' => '[elevated]',
    ];

    /**
     * Document transformers.
     *
     * @var array
     */
    protected $transformers = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_QUERYELEVATION;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Add a document transformer.
     *
     * @param string $transformer
     *
     * @return self fluent interface
     */
    public function addTransformer($transformer)
    {
        $this->transformers[$transformer] = true;

        return $this;
    }

    /**
     * Add multiple document transformers.
     *
     * You can use an array or a comma separated string as input
     *
     * @param array|string $transformers
     *
     * @return self Provides fluent interface
     */
    public function addTransformers($transformers)
    {
        if (is_string($transformers)) {
            $transformers = explode(',', $transformers);
            $transformers = array_map('trim', $transformers);
        }

        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }

        return $this;
    }

    /**
     * Remove a document transformer.
     *
     * @param string $transformer
     *
     * @return self Provides fluent interface
     */
    public function removeTransformer($transformer)
    {
        if (isset($this->transformers[$transformer])) {
            unset($this->transformers[$transformer]);
        }

        return $this;
    }

    /**
     * Remove all document transformers.
     *
     * @return self fluent interface
     */
    public function clearTransformers()
    {
        $this->transformers = [];

        return $this;
    }

    /**
     * Get all document transformers.
     *
     * @return array
     */
    public function getTransformers()
    {
        return array_keys($this->transformers);
    }

    /**
     * Set multiple document transformers.
     *
     * This overwrites any existing transformers
     *
     * @param array|string $transformers
     *
     * @return self Provides fluent interface
     */
    public function setTransformers($transformers)
    {
        $this->clearTransformers();
        $this->addTransformers($transformers);

        return $this;
    }

    /**
     * Set enable elevation.
     *
     * @param bool $enable
     *
     * @return self Provides fluent interface
     */
    public function setEnableElevation($enable)
    {
        return $this->setOption('enableElevation', $enable);
    }

    /**
     * Get enable elevation.
     *
     * @return bool
     */
    public function getEnableElevation()
    {
        return $this->getOption('enableElevation');
    }

    /**
     * Set force elevation.
     *
     * @param bool $force
     *
     * @return self Provides fluent interface
     */
    public function setForceElevation($force)
    {
        return $this->setOption('forceElevation', $force);
    }

    /**
     * Get force elevation.
     *
     * @return bool
     */
    public function getForceElevation()
    {
        return $this->getOption('forceElevation');
    }

    /**
     * Set exclusive.
     *
     * @param bool $exclusive
     *
     * @return self Provides fluent interface
     */
    public function setExclusive($exclusive)
    {
        return $this->setOption('exclusive', $exclusive);
    }

    /**
     * Get exclusive.
     *
     * @return bool
     */
    public function getExclusive()
    {
        return $this->getOption('exclusive');
    }

    /**
     * Set mark excludes.
     *
     * @param bool $mark
     *
     * @return self Provides fluent interface
     */
    public function setMarkExcludes($mark)
    {
        if (true === $mark || 'true' === $mark) {
            $this->addTransformer('[excluded]');
        } else {
            $this->removeTransformer('[excluded]');
        }

        return $this->setOption('markExcludes', $mark);
    }

    /**
     * Get mark excludes.
     *
     * @return bool
     */
    public function getMarkExcludes()
    {
        return $this->getOption('markExcludes');
    }

    /**
     * Set elevated document ids.
     *
     * @param string|array $ids can be an array or string with comma separated ids
     *
     * @return self Provides fluent interface
     */
    public function setElevateIds($ids)
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        return $this->setOption('elevateIds', $ids);
    }

    /**
     * Get elevated document ids.
     *
     * @return null|array
     */
    public function getElevateIds()
    {
        return $this->getOption('elevateIds');
    }

    /**
     * Set excluded document ids.
     *
     * @param string|array $ids can be an array or string with comma separated ids
     *
     * @return self Provides fluent interface
     */
    public function setExcludeIds($ids)
    {
        if (is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        return $this->setOption('excludeIds', $ids);
    }

    /**
     * Get excluded document ids.
     *
     * @return null|array
     */
    public function getExcludeIds()
    {
        return $this->getOption('excludeIds');
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'transformers':
                    $this->setTransformers($value);
                    break;
                case 'markExcludes':
                    $this->setMarkExcludes($value);
                    break;
                case 'elevateIds':
                    $this->setElevateIds($value);
                    break;
                case 'excludeIds':
                    $this->setExcludeIds($value);
                    break;
            }
        }
    }
}
