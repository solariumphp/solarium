<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Type;

/**
 * Analyzer base class.
 */
abstract class AbstractAnalyzer
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var CharFilter[]
     */
    protected $charFilters = [];

    /**
     * @var Tokenizer|null
     */
    protected $tokenizer = null;

    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * Constructor.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return CharFilter[]
     */
    public function getCharFilters(): array
    {
        return $this->charFilters;
    }

    /**
     * @param CharFilter[] $charFilters
     *
     * @return self
     */
    public function setCharFilters(array $charFilters): self
    {
        $this->charFilters = $charFilters;

        return $this;
    }

    /**
     * @return Tokenizer|null
     */
    public function getTokenizer(): ?Tokenizer
    {
        return $this->tokenizer;
    }

    /**
     * @param Tokenizer $tokenizer
     *
     * @return self
     */
    public function setTokenizer(Tokenizer $tokenizer): self
    {
        $this->tokenizer = $tokenizer;

        return $this;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param Filter[] $filters
     *
     * @return self
     */
    public function setFilters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function __toString(): string
    {
        return $this->className;
    }
}
