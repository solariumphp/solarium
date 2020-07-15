<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Result\Resources;

/**
 * Resource.
 */
class Resource
{
    /**
     * Resource type stopwords.
     */
    const TYPE_STOPWORDS = 'stopwords';

    /**
     * Resource type synonyms.
     */
    const TYPE_SYNONYMS = 'synonyms';

    /**
     * @var string
     */
    protected $resourceId;

    /**
     * @var int
     */
    protected $numObservers;

    /**
     * @var string
     */
    protected $class;

    /**
     * Resource constructor.
     *
     * @param array $resource
     */
    public function __construct(array $resource)
    {
        $this->resourceId = $resource['resourceId'];
        $this->numObservers = $resource['numObservers'];
        $this->class = $resource['class'];
    }

    /**
     * @return string
     */
    public function getResourceId(): string
    {
        return $this->resourceId;
    }

    /**
     * @param string $resourceId
     *
     * @return self
     */
    public function setResourceId(string $resourceId): self
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumObservers(): int
    {
        return $this->numObservers;
    }

    /**
     * @param int $numObservers
     *
     * @return self
     */
    public function setNumObservers(int $numObservers): self
    {
        $this->numObservers = $numObservers;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /*
     * Returns the type: 'stopwords', 'synonyms' or '' if unknown.
     *
     * @return string
     */
    public function getType(): string
    {
        if (0 === strncmp($this->resourceId, '/schema/analysis/stopwords', \strlen('/schema/analysis/stopwords'))) {
            return self::TYPE_STOPWORDS;
        } elseif (0 === strncmp($this->resourceId, '/schema/analysis/synonyms', \strlen('/schema/analysis/synonyms'))) {
            return self::TYPE_SYNONYMS;
        }

        return '';
    }
}
