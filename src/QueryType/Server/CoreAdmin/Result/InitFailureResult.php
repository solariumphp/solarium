<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Server\CoreAdmin\Result;

/**
 * Retrieved init failure.
 */
class InitFailureResult
{
    /**
     * @var string
     */
    protected $coreName;

    /**
     * @var string
     */
    protected $exception;

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
     * @return string
     */
    public function getException(): string
    {
        return $this->exception;
    }

    /**
     * @param string $exception
     *
     * @return self
     */
    public function setException(string $exception): self
    {
        $this->exception = $exception;

        return $this;
    }
}
