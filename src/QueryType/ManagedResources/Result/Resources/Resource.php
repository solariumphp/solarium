<?php

namespace Solarium\QueryType\ManagedResources\Result\Resources;

class Resource
{
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
     * Resource type stopwords.
     */
    const TYPE_STOPWORDS = 'stopwords';

    /**
     * Resource type synonyms.
     */
    const TYPE_SYNONYMS = 'synonyms';

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
     */
    public function setResourceId(string $resourceId)
    {
        $this->resourceId = $resourceId;
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
     */
    public function setNumObservers(int $numObservers)
    {
        $this->numObservers = $numObservers;
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
     */
    public function setClass(string $class)
    {
        $this->class = $class;
    }

    /*
     * Returns the type: 'stopwords', 'synonyms' or '' if unknown.
     *
     * @return string
     */
    public function getType(): string
    {
        if (0 === strncmp($this->resourceId, '/schema/analysis/stopwords', strlen('/schema/analysis/stopwords'))) {
            return self::TYPE_STOPWORDS;
        } elseif (0 === strncmp($this->resourceId, '/schema/analysis/synonyms', strlen('/schema/analysis/synonyms'))) {
            return self::TYPE_SYNONYMS;
        }

        return '';
    }
}
