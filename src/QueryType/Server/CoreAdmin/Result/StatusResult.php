<?php

namespace Solarium\QueryType\Server\CoreAdmin\Result;

/**
 * Retrieved status information.
 */
class StatusResult
{
    /**
     * @var string
     */
    protected $coreName = '';

    /**
     * @var int
     */
    protected $numberOfDocuments = 0;

    /**
     * @var int
     */
    protected $uptime = 0;

    /**
     * @var int
     */
    protected $version = 0;

    /**
     * @var \DateTime|null
     */
    protected $startTime = null;

    /**
     * @var \DateTime|null
     */
    protected $lastModified = null;

    /**
     * @return string
     */
    public function getCoreName(): string
    {
        return $this->coreName;
    }

    /**
     * @param string $coreName
     */
    public function setCoreName(string $coreName)
    {
        $this->coreName = $coreName;
    }

    /**
     * @return int
     */
    public function getNumberOfDocuments(): int
    {
        return $this->numberOfDocuments;
    }

    /**
     * @param int $numberOfDocuments
     */
    public function setNumberOfDocuments(int $numberOfDocuments)
    {
        $this->numberOfDocuments = $numberOfDocuments;
    }

    /**
     * @return int
     */
    public function getUptime(): int
    {
        return $this->uptime;
    }

    /**
     * @param int $uptime
     */
    public function setUptime(int $uptime)
    {
        $this->uptime = $uptime;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version)
    {
        $this->version = $version;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime|null $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param \DateTime|null $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }
}
