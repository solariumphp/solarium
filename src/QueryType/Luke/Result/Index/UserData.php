<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Index;

/**
 * User data.
 *
 * Contains information on the latest commit.
 */
class UserData
{
    /**
     * @var string|null
     */
    protected $commitCommandVer = null;

    /**
     * @var string|null
     */
    protected $commitTimeMSec = null;

    /**
     * @return string|null
     */
    public function getCommitCommandVer(): ?string
    {
        return $this->commitCommandVer;
    }

    /**
     * @param string|null $commitCommandVer
     *
     * @return self Provides fluent interface
     */
    public function setCommitCommandVer(?string $commitCommandVer): self
    {
        $this->commitCommandVer = $commitCommandVer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCommitTimeMSec(): ?string
    {
        return $this->commitTimeMSec;
    }

    /**
     * @param string|null $commitTimeMSec
     *
     * @return self Provides fluent interface
     */
    public function setCommitTimeMSec(?string $commitTimeMSec): self
    {
        $this->commitTimeMSec = $commitTimeMSec;

        return $this;
    }
}
