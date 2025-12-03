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
    protected string $coreName = '';

    protected int $numberOfDocuments = 0;

    protected int $uptime = 0;

    protected int $version = 0;

    protected ?\DateTime $startTime;

    protected ?\DateTime $lastModified;

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
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
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
     * @return self Provides fluent interface
     */
    public function setLastModified(?\DateTime $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }
}
