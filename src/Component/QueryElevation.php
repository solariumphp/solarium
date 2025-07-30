<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\QueryElevation as RequestBuilder;

/**
 * QueryElevation component.
 *
 * @see https://solr.apache.org/guide/the-query-elevation-component.html
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
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_QUERYELEVATION;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Add a document transformer.
     *
     * @param string $transformer
     *
     * @return self Provides fluent interface
     */
    public function addTransformer(string $transformer): self
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
    public function addTransformers($transformers): self
    {
        if (\is_string($transformers)) {
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
    public function removeTransformer(string $transformer): self
    {
        if (isset($this->transformers[$transformer])) {
            unset($this->transformers[$transformer]);
        }

        return $this;
    }

    /**
     * Remove all document transformers.
     *
     * @return self Provides fluent interface
     */
    public function clearTransformers(): self
    {
        $this->transformers = [];

        return $this;
    }

    /**
     * Get all document transformers.
     *
     * @return array
     */
    public function getTransformers(): array
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
    public function setTransformers($transformers): self
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
    public function setEnableElevation(bool $enable): self
    {
        $this->setOption('enableElevation', $enable);

        return $this;
    }

    /**
     * Get enable elevation.
     *
     * @return bool|null
     */
    public function getEnableElevation(): ?bool
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
    public function setForceElevation(bool $force): self
    {
        $this->setOption('forceElevation', $force);

        return $this;
    }

    /**
     * Get force elevation.
     *
     * @return bool|null
     */
    public function getForceElevation(): ?bool
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
    public function setExclusive(bool $exclusive): self
    {
        $this->setOption('exclusive', $exclusive);

        return $this;
    }

    /**
     * Get exclusive.
     *
     * @return bool|null
     */
    public function getExclusive(): ?bool
    {
        return $this->getOption('exclusive');
    }

    /**
     * Set use configured elevated order.
     *
     * @param bool $useConfiguredElevatedOrder
     *
     * @return self Provides fluent interface
     */
    public function setUseConfiguredElevatedOrder(bool $useConfiguredElevatedOrder): self
    {
        $this->setOption('useConfiguredElevatedOrder', $useConfiguredElevatedOrder);

        return $this;
    }

    /**
     * Get use configured elevated order.
     *
     * @return bool|null
     */
    public function getUseConfiguredElevatedOrder(): ?bool
    {
        return $this->getOption('useConfiguredElevatedOrder');
    }

    /**
     * Set mark excludes.
     *
     * @param bool $mark
     *
     * @return self Provides fluent interface
     */
    public function setMarkExcludes(bool $mark): self
    {
        if ($mark) {
            $this->addTransformer('[excluded]');
        } else {
            $this->removeTransformer('[excluded]');
        }

        $this->setOption('markExcludes', $mark);

        return $this;
    }

    /**
     * Get mark excludes.
     *
     * @return bool|null
     */
    public function getMarkExcludes(): ?bool
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
    public function setElevateIds($ids): self
    {
        if (\is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $this->setOption('elevateIds', $ids);

        return $this;
    }

    /**
     * Get elevated document ids.
     *
     * @return array|null
     */
    public function getElevateIds(): ?array
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
    public function setExcludeIds($ids): self
    {
        if (\is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        $this->setOption('excludeIds', $ids);

        return $this;
    }

    /**
     * Get excluded document ids.
     *
     * @return array|null
     */
    public function getExcludeIds(): ?array
    {
        return $this->getOption('excludeIds');
    }

    /**
     * Set tags of filter queries to exclude for elevated documents.
     *
     * @param string|array $tags can be an array or string with comma separated tags
     *
     * @return self Provides fluent interface
     */
    public function setExcludeTags($tags): self
    {
        if (\is_string($tags)) {
            $tags = explode(',', $tags);
            $tags = array_map('trim', $tags);
        }

        $this->setOption('excludeTags', $tags);

        return $this;
    }

    /**
     * Get tags of filter queries to exclude for elevated documents.
     *
     * @return array|null
     */
    public function getExcludeTags(): ?array
    {
        return $this->getOption('excludeTags');
    }

    /**
     * Initialize options.
     *
     * {@internal Options that influence transformers need additional setup work.
     *            Options that set a list of ids need additional setup work
     *            because they can be an array or a comma separated string.}
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
                case 'excludeTags':
                    $this->setExcludeTags($value);
                    break;
            }
        }
    }
}
