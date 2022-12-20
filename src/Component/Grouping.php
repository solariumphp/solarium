<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Grouping as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Grouping as ResponseParser;
use Solarium\Component\Result\Grouping\QueryGroup;
use Solarium\Component\Result\Grouping\ValueGroup;

/**
 * Grouping component.
 *
 * Also known as Result Grouping or Field Collapsing.
 * See the Solr wiki for more info about this functionality
 *
 * @see https://solr.apache.org/guide/result-grouping.html
 */
class Grouping extends AbstractComponent
{
    /**
     * Value for format grouped.
     */
    const FORMAT_GROUPED = 'grouped';

    /**
     * Value for format simple.
     */
    const FORMAT_SIMPLE = 'simple';

    /**
     * Component type.
     *
     * @var string
     */
    protected $type = ComponentAwareQueryInterface::COMPONENT_GROUPING;

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'resultquerygroupclass' => QueryGroup::class,
        'resultvaluegroupclass' => ValueGroup::class,
    ];

    /**
     * Fields for grouping.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Queries for grouping.
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_GROUPING;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Add a grouping field.
     *
     * Group based on the unique values of a field
     *
     * @param string $field
     *
     * @return self fluent interface
     */
    public function addField(string $field): self
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Add multiple grouping fields.
     *
     * You can use an array or a comma separated string as input
     *
     * @param array|string $fields
     *
     * @return self Provides fluent interface
     */
    public function addFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $this->fields = array_merge($this->fields, $fields);

        return $this;
    }

    /**
     * Get all fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Remove all fields.
     *
     * @return self fluent interface
     */
    public function clearFields(): self
    {
        $this->fields = [];

        return $this;
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param string|array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Add a grouping query.
     *
     * Group documents that match the given query
     *
     * @param string $query
     *
     * @return self fluent interface
     */
    public function addQuery(string $query): self
    {
        $this->queries[] = $query;

        return $this;
    }

    /**
     * Add multiple grouping queries.
     *
     * @param array|string $queries
     *
     * @return self Provides fluent interface
     */
    public function addQueries($queries): self
    {
        if (!\is_array($queries)) {
            $queries = [$queries];
        }

        $this->queries = array_merge($this->queries, $queries);

        return $this;
    }

    /**
     * Get all queries.
     *
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * Remove all queries.
     *
     * @return self fluent interface
     */
    public function clearQueries(): self
    {
        $this->queries = [];

        return $this;
    }

    /**
     * Set multiple queries.
     *
     * This overwrites any existing queries
     *
     * @param array $queries
     *
     * @return self Provides fluent interface
     */
    public function setQueries($queries): self
    {
        $this->clearQueries();
        $this->addQueries($queries);

        return $this;
    }

    /**
     * Set limit option.
     *
     * The number of results (documents) to return for each group
     *
     * @param int $limit
     *
     * @return self Provides fluent interface
     */
    public function setLimit(int $limit): self
    {
        $this->setOption('limit', $limit);

        return $this;
    }

    /**
     * Get limit option.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->getOption('limit');
    }

    /**
     * Set offset option.
     *
     * The offset into the document list of each group.
     *
     * @param int $offset
     *
     * @return self Provides fluent interface
     */
    public function setOffset(int $offset): self
    {
        $this->setOption('offset', $offset);

        return $this;
    }

    /**
     * Get offset option.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->getOption('offset');
    }

    /**
     * Set sort option.
     *
     * How to sort documents within a single group
     *
     * @param string $sort
     *
     * @return self Provides fluent interface
     */
    public function setSort(string $sort): self
    {
        $this->setOption('sort', $sort);

        return $this;
    }

    /**
     * Get sort option.
     *
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->getOption('sort');
    }

    /**
     * Set mainresult option.
     *
     * If true, the result of the first field grouping command is used as the main
     * result list in the response, using group format 'simple'
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setMainResult(bool $value): self
    {
        $this->setOption('mainresult', $value);

        return $this;
    }

    /**
     * Get mainresult option.
     *
     * @return bool|null
     */
    public function getMainResult(): ?bool
    {
        return $this->getOption('mainresult');
    }

    /**
     * Set numberofgroups option.
     *
     * If true, includes the number of groups that have matched the query.
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setNumberOfGroups(bool $value): self
    {
        $this->setOption('numberofgroups', $value);

        return $this;
    }

    /**
     * Get numberofgroups option.
     *
     * @return bool|null
     */
    public function getNumberOfGroups(): ?bool
    {
        return $this->getOption('numberofgroups');
    }

    /**
     * Set cachepercentage option.
     *
     * If > 0 enables grouping cache. Grouping is executed actual two searches.
     * This option caches the second search. A value of 0 disables grouping caching.
     *
     * Tests have shown that this cache only improves search time with boolean queries,
     * wildcard queries and fuzzy queries. For simple queries like a term query or
     * a match all query this cache has a negative impact on performance
     *
     * @param int $value
     *
     * @return self Provides fluent interface
     */
    public function setCachePercentage(int $value): self
    {
        $this->setOption('cachepercentage', $value);

        return $this;
    }

    /**
     * Get cachepercentage option.
     *
     * @return int|null
     */
    public function getCachePercentage(): ?int
    {
        return $this->getOption('cachepercentage');
    }

    /**
     * Set truncate option.
     *
     * If true, facet counts are based on the most relevant document of each group matching the query.
     * Same applies for StatsComponent. Default is false. Only available from Solr 3.4
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setTruncate(bool $value): self
    {
        $this->setOption('truncate', $value);

        return $this;
    }

    /**
     * Get truncate option.
     *
     * @return bool|null
     */
    public function getTruncate(): ?bool
    {
        return $this->getOption('truncate');
    }

    /**
     * Set function option.
     *
     * Group based on the unique values of a function query. Only available in Solr 4.0+
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setFunction(string $value): self
    {
        $this->setOption('function', $value);

        return $this;
    }

    /**
     * Get truncate option.
     *
     * @return string|null
     */
    public function getFunction(): ?string
    {
        return $this->getOption('function');
    }

    /**
     * Set facet option.
     *
     * Whether to compute grouped facets.
     * Grouped facets are computed based on the first specified group.
     * This parameter only is supported on Solr 4.0+
     *
     * @param bool $value
     *
     * @return self Provides fluent interface
     */
    public function setFacet(bool $value): self
    {
        $this->setOption('facet', $value);

        return $this;
    }

    /**
     * Get facet option.
     *
     * @return bool|null
     */
    public function getFacet(): ?bool
    {
        return $this->getOption('facet');
    }

    /**
     * Set format option.
     *
     * If simple, the grouped documents are presented in a single flat list.
     * The start and rows parameters refer to numbers of documents instead of numbers of groups.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setFormat(string $value): self
    {
        $this->setOption('format', $value);

        return $this;
    }

    /**
     * Get format option.
     *
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->getOption('format');
    }

    /**
     * Set the query group result class.
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setResultQueryGroupClass(string $value): self
    {
        $this->setOption('resultquerygroupclass', $value);

        return $this;
    }

    /**
     * Get the current resultquerygroupclass option.
     *
     * The value is a classname, not an instance
     *
     * @return string|null
     */
    public function getResultQueryGroupClass(): ?string
    {
        return $this->getOption('resultquerygroupclass');
    }

    /**
     * Set the value group result class.
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setResultValueGroupClass(string $value): self
    {
        $this->setOption('resultvaluegroupclass', $value);

        return $this;
    }

    /**
     * Get the current resultvaluegroupclass option.
     *
     * The value is a classname, not an instance
     *
     * @return string|null
     */
    public function getResultValueGroupClass(): ?string
    {
        return $this->getOption('resultvaluegroupclass');
    }

    /**
     * Initialize options.
     *
     * {@internal The 'query' option needs additional setup work.
     *            Options that set a list of fields need additional setup work
     *            because they can be an array or a comma separated string.}
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'queries':
                    $this->setQueries($value);
                    break;
                case 'fields':
                    $this->setFields($value);
                    break;
            }
        }
    }
}
