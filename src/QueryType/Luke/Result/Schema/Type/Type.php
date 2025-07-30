<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke\Result\Schema\Type;

use Solarium\QueryType\Luke\Result\Schema\Field\SchemaFieldInterface;
use Solarium\QueryType\Luke\Result\Schema\Similarity;

/**
 * Retrieved field type definition.
 */
class Type
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var SchemaFieldInterface[]
     */
    protected $fields = [];

    /**
     * @var bool
     */
    protected $tokenized;

    /**
     * @var string
     */
    protected $className;

    /**
     * @var IndexAnalyzer
     */
    protected $indexAnalyzer;

    /**
     * @var QueryAnalyzer
     */
    protected $queryAnalyzer;

    /**
     * @var Similarity
     */
    protected $similarity;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return SchemaFieldInterface[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param SchemaFieldInterface $field
     *
     * @return self Provides fluent interface
     */
    public function addField(SchemaFieldInterface &$field): self
    {
        $this->fields[] = &$field;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTokenized(): bool
    {
        return $this->tokenized;
    }

    /**
     * @param bool $tokenized
     *
     * @return self Provides fluent interface
     */
    public function setTokenized(bool $tokenized): self
    {
        $this->tokenized = $tokenized;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTokenized(): bool
    {
        return $this->tokenized;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     *
     * @return self Provides fluent interface
     */
    public function setClassName(string $className): self
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return IndexAnalyzer
     */
    public function getIndexAnalyzer(): IndexAnalyzer
    {
        return $this->indexAnalyzer;
    }

    /**
     * @param IndexAnalyzer $indexAnalyzer
     *
     * @return self Provides fluent interface
     */
    public function setIndexAnalyzer(IndexAnalyzer $indexAnalyzer): self
    {
        $this->indexAnalyzer = $indexAnalyzer;

        return $this;
    }

    /**
     * @return QueryAnalyzer
     */
    public function getQueryAnalyzer(): QueryAnalyzer
    {
        return $this->queryAnalyzer;
    }

    /**
     * @param QueryAnalyzer $queryAnalyzer
     *
     * @return self Provides fluent interface
     */
    public function setQueryAnalyzer(QueryAnalyzer $queryAnalyzer): self
    {
        $this->queryAnalyzer = $queryAnalyzer;

        return $this;
    }

    /**
     * @return Similarity
     */
    public function getSimilarity(): Similarity
    {
        return $this->similarity;
    }

    /**
     * @param Similarity $similarity
     *
     * @return self Provides fluent interface
     */
    public function setSimilarity(Similarity $similarity): self
    {
        $this->similarity = $similarity;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
