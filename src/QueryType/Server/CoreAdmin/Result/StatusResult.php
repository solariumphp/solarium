<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
    protected $startTime;

    /**
     * @var \DateTime|null
     */
    protected $lastModified;

    /**
     * @return string
     */
    public function getCoreName(): string
    {
        return $this->coreName;
    }

    /**
     * @param string $coreName
     *
     * @return self
     */
    public function setCoreName(string $coreName): self
    {
        $this->coreName = $coreName;

        return $this;
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
     *
     * @return self
     */
    public function setNumberOfDocuments(int $numberOfDocuments): self
    {
        $this->numberOfDocuments = $numberOfDocuments;

        return $this;
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
     *
     * @return self
     */
    public function setUptime(int $uptime): self
    {
        $this->uptime = $uptime;

        return $this;
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
     *
     * @return self
     */
    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime|null $startTime
     *
     * @return self
     */
    public function setStartTime(?\DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastModified(): ?\DateTime
    {
        return $this->lastModified;
    }

    /**
     * @param \DateTime|null $lastModified
     *
     * @return $this
     */
    public function setLastModified(?\DateTime $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }
}
