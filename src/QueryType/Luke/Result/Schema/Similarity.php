<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema;

/**
 * Similarity.
 */
class Similarity
{
    /**
     * @var string|null
     */
    protected $className = null;

    /**
     * @var string|null
     */
    protected $details = null;

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param string|null $className
     *
     * @return self Provides fluent interface
     */
    public function setClassName(?string $className): self
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @param string|null $details
     *
     * @return self Provides fluent interface
     */
    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function __toString(): string
    {
        return $this->className ?? '';
    }
}
