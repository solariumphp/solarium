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
}