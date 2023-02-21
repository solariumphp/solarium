<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Index;

/**
 * Retrieved index information.
 */
class Index
{
    /**
     * @var int
     */
    protected $numDocs;

    /**
     * @var int
     */
    protected $maxDoc;

    /**
     * @var int
     */
    protected $deletedDocs;

    /**
     * @var int|null
     */
    protected $indexHeapUsageBytes;

    /**
     * @var int
     */
    protected $version;

    /**
     * @var int
     */
    protected $segmentCount;

    /**
     * @var bool
     */
    protected $current;

    /**
     * @var bool
     */
    protected $hasDeletions;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $segmentsFile;

    /**
     * @var int
     */
    protected $segmentsFileSizeInBytes;

    /**
     * @var UserData
     */
    protected $userData;

    /**
     * @var \DateTime|null
     */
    protected $lastModified;

    /**
     * @return int
     */
    public function getNumDocs(): int
    {
        return $this->numDocs;
    }

    /**
     * @param int $numDocs
     *
     * @return self
     */
    public function setNumDocs(int $numDocs): self
    {
        $this->numDocs = $numDocs;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxDoc(): int
    {
        return $this->maxDoc;
    }

    /**
     * @param int $maxDoc
     *
     * @return self
     */
    public function setMaxDoc(int $maxDoc): self
    {
        $this->maxDoc = $maxDoc;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeletedDocs(): int
    {
        return $this->deletedDocs;
    }

    /**
     * @param int $deletedDocs
     *
     * @return self
     */
    public function setDeletedDocs(int $deletedDocs): self
    {
        $this->deletedDocs = $deletedDocs;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIndexHeapUsageBytes(): ?int
    {
        return $this->indexHeapUsageBytes;
    }

    /**
     * @param int|null $indexHeapUsageBytes
     *
     * @return self
     */
    public function setIndexHeapUsageBytes(?int $indexHeapUsageBytes): self
    {
        $this->indexHeapUsageBytes = $indexHeapUsageBytes;

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
     * @return int
     */
    public function getSegmentCount(): int
    {
        return $this->segmentCount;
    }

    /**
     * @param int $segmentCount
     *
     * @return self
     */
    public function setSegmentCount(int $segmentCount): self
    {
        $this->segmentCount = $segmentCount;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @param bool $current
     *
     * @return self
     */
    public function setCurrent(bool $current): self
    {
        $this->current = $current;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->current;
    }

    /**
     * @return bool
     */
    public function getHasDeletions(): bool
    {
        return $this->hasDeletions;
    }

    /**
     * @param bool $hasDeletions
     *
     * @return self
     */
    public function setHasDeletions(bool $hasDeletions): self
    {
        $this->hasDeletions = $hasDeletions;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDeletions(): bool
    {
        return $this->hasDeletions;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @param string $directory
     *
     * @return self
     */
    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @return string
     */
    public function getSegmentsFile(): string
    {
        return $this->segmentsFile;
    }

    /**
     * @param string $segmentsFile
     *
     * @return self
     */
    public function setSegmentsFile(string $segmentsFile): self
    {
        $this->segmentsFile = $segmentsFile;

        return $this;
    }

    /**
     * @return int
     */
    public function getSegmentsFileSizeInBytes(): int
    {
        return $this->segmentsFileSizeInBytes;
    }

    /**
     * @param int $segmentsFileSizeInBytes
     *
     * @return self
     */
    public function setSegmentsFileSizeInBytes(int $segmentsFileSizeInBytes): self
    {
        $this->segmentsFileSizeInBytes = $segmentsFileSizeInBytes;

        return $this;
    }

    /**
     * @return UserData
     */
    public function getUserData(): UserData
    {
        return $this->userData;
    }

    /**
     * @param UserData $userData
     *
     * @return self
     */
    public function setUserData(UserData $userData): self
    {
        $this->userData = $userData;

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
