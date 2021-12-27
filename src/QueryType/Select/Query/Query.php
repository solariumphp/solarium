<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Select\Query;

use Solarium\Component\Analytics\Analytics;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\ComponentAwareQueryTrait;
use Solarium\Component\Debug;
use Solarium\Component\DisMax;
use Solarium\Component\DistributedSearch;
use Solarium\Component\EdisMax;
use Solarium\Component\FacetSet;
use Solarium\Component\Grouping;
use Solarium\Component\Highlighting\Highlighting;
use Solarium\Component\MoreLikeThis;
use Solarium\Component\QueryElevation;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Component\QueryTraits\AnalyticsTrait;
use Solarium\Component\QueryTraits\DebugTrait;
use Solarium\Component\QueryTraits\DisMaxTrait;
use Solarium\Component\QueryTraits\DistributedSearchTrait;
use Solarium\Component\QueryTraits\EDisMaxTrait;
use Solarium\Component\QueryTraits\FacetSetTrait;
use Solarium\Component\QueryTraits\GroupingTrait;
use Solarium\Component\QueryTraits\HighlightingTrait;
use Solarium\Component\QueryTraits\MoreLikeThisTrait;
use Solarium\Component\QueryTraits\QueryElevationTrait;
use Solarium\Component\QueryTraits\ReRankQueryTrait;
use Solarium\Component\QueryTraits\SpatialTrait;
use Solarium\Component\QueryTraits\SpellcheckTrait;
use Solarium\Component\QueryTraits\StatsTrait;
use Solarium\Component\QueryTraits\SuggesterTrait;
use Solarium\Component\ReRankQuery;
use Solarium\Component\Spatial;
use Solarium\Component\Spellcheck;
use Solarium\Component\Stats\Stats;
use Solarium\Component\Suggester;
use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;
use Solarium\QueryType\Select\RequestBuilder;
use Solarium\QueryType\Select\ResponseParser;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;

/**
 * Select Query.
 *
 * Can be used to select documents and/or facets from Solr. This querytype has
 * lots of options and there are many Solarium subclasses for it.
 * See the Solr documentation and the relevant Solarium classes for more info.
 */
class Query extends AbstractQuery implements ComponentAwareQueryInterface, QueryInterface
{
    use AnalyticsTrait;
    use ComponentAwareQueryTrait;
    use DebugTrait;
    use DisMaxTrait;
    use DistributedSearchTrait;
    use EDisMaxTrait;
    use FacetSetTrait;
    use GroupingTrait;
    use HighlightingTrait;
    use MoreLikeThisTrait;
    use QueryElevationTrait;
    use QueryTrait;
    use ReRankQueryTrait;
    use SpatialTrait;
    use SpellcheckTrait;
    use StatsTrait;
    use SuggesterTrait;

    /**
     * Solr sort mode descending.
     */
    const SORT_DESC = 'desc';

    /**
     * Solr sort mode ascending.
     */
    const SORT_ASC = 'asc';

    /**
     * Solr query operator AND.
     */
    const QUERY_OPERATOR_AND = 'AND';

    /**
     * Solr query operator OR.
     */
    const QUERY_OPERATOR_OR = 'OR';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'select',
        'resultclass' => Result::class,
        'documentclass' => Document::class,
        'query' => '*:*',
        'start' => 0,
        'rows' => 10,
        'fields' => '*,score',
        'omitheader' => true,
    ];

    /**
     * Tags for this query.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Fields to fetch.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Items to sort on.
     *
     * @var array
     */
    protected $sorts = [];

    /**
     * Filterqueries.
     *
     * @var FilterQuery[]
     */
    protected $filterQueries = [];

    /**
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $this->componentTypes = [
            ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS => MoreLikeThis::class,
            ComponentAwareQueryInterface::COMPONENT_SPELLCHECK => Spellcheck::class,
            ComponentAwareQueryInterface::COMPONENT_SUGGESTER => Suggester::class,
            ComponentAwareQueryInterface::COMPONENT_DEBUG => Debug::class,
            ComponentAwareQueryInterface::COMPONENT_SPATIAL => Spatial::class,
            ComponentAwareQueryInterface::COMPONENT_FACETSET => FacetSet::class,
            ComponentAwareQueryInterface::COMPONENT_DISMAX => DisMax::class,
            ComponentAwareQueryInterface::COMPONENT_EDISMAX => EdisMax::class,
            ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING => Highlighting::class,
            ComponentAwareQueryInterface::COMPONENT_GROUPING => Grouping::class,
            ComponentAwareQueryInterface::COMPONENT_DISTRIBUTEDSEARCH => DistributedSearch::class,
            ComponentAwareQueryInterface::COMPONENT_STATS => Stats::class,
            ComponentAwareQueryInterface::COMPONENT_QUERYELEVATION => QueryElevation::class,
            ComponentAwareQueryInterface::COMPONENT_RERANKQUERY => ReRankQuery::class,
            ComponentAwareQueryInterface::COMPONENT_ANALYTICS => Analytics::class,
        ];

        parent::__construct($options);
    }

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_SELECT;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Set default query operator.
     *
     * Use one of the constants as value
     *
     * @param string $operator
     *
     * @return self Provides fluent interface
     */
    public function setQueryDefaultOperator(string $operator): self
    {
        $this->setOption('querydefaultoperator', $operator);

        return $this;
    }

    /**
     * Get the default query operator.
     *
     * @return string|null
     */
    public function getQueryDefaultOperator(): ?string
    {
        return $this->getOption('querydefaultoperator');
    }

    /**
     * Set default query field.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function setQueryDefaultField(string $field): self
    {
        $this->setOption('querydefaultfield', $field);

        return $this;
    }

    /**
     * Get the default query field.
     *
     * @return string|null
     */
    public function getQueryDefaultField(): ?string
    {
        return $this->getOption('querydefaultfield');
    }

    /**
     * Set the start offset.
     *
     * @param int $start
     *
     * @return self Provides fluent interface
     */
    public function setStart(int $start): self
    {
        $this->setOption('start', $start);

        return $this;
    }

    /**
     * Get the start offset.
     *
     * @return int|null
     */
    public function getStart(): ?int
    {
        return $this->getOption('start');
    }

    /**
     * Set a custom document class.
     *
     * This class should implement the document interface
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setDocumentClass(string $value): self
    {
        $this->setOption('documentclass', $value);

        return $this;
    }

    /**
     * Get the current documentclass option.
     *
     * The value is a classname, not an instance
     *
     * @return string|null
     */
    public function getDocumentClass(): ?string
    {
        return $this->getOption('documentclass');
    }

    /**
     * Set the number of rows to fetch.
     *
     * @param int $rows
     *
     * @return self Provides fluent interface
     */
    public function setRows(int $rows): self
    {
        $this->setOption('rows', $rows);

        return $this;
    }

    /**
     * Get the number of rows.
     *
     * @return int|null
     */
    public function getRows(): ?int
    {
        return $this->getOption('rows');
    }

    /**
     * Specify a field to return in the resultset.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function addField(string $field): self
    {
        $this->fields[$field] = true;

        return $this;
    }

    /**
     * Specify multiple fields to return in the resultset.
     *
     * @param string|array $fields can be an array or string with comma
     *                             separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function addFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Remove a field from the field list.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function removeField(string $field): self
    {
        if (isset($this->fields[$field])) {
            unset($this->fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields from the field list.
     *
     * @return self Provides fluent interface
     */
    public function clearFields(): self
    {
        $this->fields = [];

        return $this;
    }

    /**
     * Get the list of fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return array_keys($this->fields);
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param string|array $fields can be an array or string with comma separated field names
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
     * Add a sort.
     *
     * @param string $sort
     * @param string $order
     *
     * @return self Provides fluent interface
     */
    public function addSort(string $sort, string $order): self
    {
        $this->sorts[$sort] = $order;

        return $this;
    }

    /**
     * Add multiple sorts.
     *
     * The input array must contain sort items as keys and the order as values.
     *
     * @param array $sorts
     *
     * @return self Provides fluent interface
     */
    public function addSorts(array $sorts): self
    {
        foreach ($sorts as $sort => $order) {
            $this->addSort($sort, $order);
        }

        return $this;
    }

    /**
     * Remove a sort.
     *
     * @param string $sort
     *
     * @return self Provides fluent interface
     */
    public function removeSort(string $sort): self
    {
        if (isset($this->sorts[$sort])) {
            unset($this->sorts[$sort]);
        }

        return $this;
    }

    /**
     * Remove all sorts.
     *
     * @return self Provides fluent interface
     */
    public function clearSorts(): self
    {
        $this->sorts = [];

        return $this;
    }

    /**
     * Get a list of the sorts.
     *
     * @return array
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * Set multiple sorts.
     *
     * This overwrites any existing sorts
     *
     * @param array $sorts
     *
     * @return self Provides fluent interface
     */
    public function setSorts(array $sorts): self
    {
        $this->clearSorts();
        $this->addSorts($sorts);

        return $this;
    }

    /**
     * Create a filterquery instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the filterquery
     * and it will be added to this query.
     * If you supply an options array/object that contains a key the filterquery will also be added to the query.
     *
     * When no key is supplied the filterquery cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addFilterQuery method.
     *
     * @param mixed $options
     *
     * @return FilterQuery
     */
    public function createFilterQuery($options = null): FilterQuery
    {
        if (\is_string($options)) {
            $fq = new FilterQuery();
            $fq->setKey($options);
        } else {
            $fq = new FilterQuery($options);
        }

        if (null !== $fq->getKey()) {
            $this->addFilterQuery($fq);
        }

        return $fq;
    }

    /**
     * Add a filter query.
     *
     * Supports a filterquery instance or a config array, in that case a new
     * filterquery instance wil be created based on the options.
     *
     * @param FilterQuery|array $filterQuery
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addFilterQuery($filterQuery): self
    {
        if (\is_array($filterQuery)) {
            $filterQuery = new FilterQuery($filterQuery);
        }

        $key = $filterQuery->getKey();

        if (null === $key || 0 === \strlen($key)) {
            throw new InvalidArgumentException('A filterquery must have a key value');
        }

        //double add calls for the same FQ are ignored, but non-unique keys cause an exception
        if (\array_key_exists($key, $this->filterQueries) && $this->filterQueries[$key] !== $filterQuery) {
            throw new InvalidArgumentException('A filterquery must have a unique key value within a query');
        }

        $this->filterQueries[$key] = $filterQuery;

        return $this;
    }

    /**
     * Add multiple filterqueries.
     *
     * @param array $filterQueries
     *
     * @return self Provides fluent interface
     */
    public function addFilterQueries(array $filterQueries): self
    {
        foreach ($filterQueries as $key => $filterQuery) {
            // in case of a config array: add key to config
            if (\is_array($filterQuery) && !isset($filterQuery['key'])) {
                $filterQuery['key'] = $key;
            }

            $this->addFilterQuery($filterQuery);
        }

        return $this;
    }

    /**
     * Get a filterquery.
     *
     * @param string $key
     *
     * @return FilterQuery|null
     */
    public function getFilterQuery(string $key): ?FilterQuery
    {
        return $this->filterQueries[$key] ?? null;
    }

    /**
     * Get all filterqueries.
     *
     * @return FilterQuery[]
     */
    public function getFilterQueries(): array
    {
        return $this->filterQueries;
    }

    /**
     * Remove a single filterquery.
     *
     * You can remove a filterquery by passing its key, or by passing the filterquery instance
     *
     * @param string|FilterQuery $filterQuery
     *
     * @return self Provides fluent interface
     */
    public function removeFilterQuery($filterQuery): self
    {
        if (\is_object($filterQuery)) {
            $filterQuery = $filterQuery->getKey();
        }

        if (isset($this->filterQueries[$filterQuery])) {
            unset($this->filterQueries[$filterQuery]);
        }

        return $this;
    }

    /**
     * Remove all filterqueries.
     *
     * @return self Provides fluent interface
     */
    public function clearFilterQueries(): self
    {
        $this->filterQueries = [];

        return $this;
    }

    /**
     * Set multiple filterqueries.
     *
     * This overwrites any existing filterqueries
     *
     * @param array $filterQueries
     *
     * @return self Provides fluent interface
     */
    public function setFilterQueries(array $filterQueries): self
    {
        $this->clearFilterQueries();
        $this->addFilterQueries($filterQueries);

        return $this;
    }

    /**
     * Add a tag.
     *
     * @param string $tag
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function addTag(string $tag): self
    {
        $this
            ->getLocalParameters()
            ->addTags([$tag])
        ;

        return $this;
    }

    /**
     * Add tags.
     *
     * @param array $tags
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function addTags(array $tags): self
    {
        $this
            ->getLocalParameters()
            ->addTags($tags)
        ;

        return $this;
    }

    /**
     * Get all tagss.
     *
     * @throws OutOfBoundsException
     *
     * @return array
     */
    public function getTags(): array
    {
        return $this
            ->getLocalParameters()
            ->getTags()
        ;
    }

    /**
     * Remove a tag.
     *
     * @param string $tag
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function removeTag(string $tag): self
    {
        $this
            ->getLocalParameters()
            ->removeTag($tag)
        ;

        return $this;
    }

    /**
     * Remove all tags.
     *
     * @throws OutOfBoundsException
     *
     * @return self Provides fluent interface
     */
    public function clearTags(): self
    {
        $this
            ->getLocalParameters()
            ->clearTags()
        ;

        return $this;
    }

    /**
     * Set multiple tags.
     *
     * This overwrites any existing tags
     *
     * @param array $tags
     *
     * @throws OutOfBoundsException
     *
     * @return $this
     */
    public function setTags(array $tags): self
    {
        $this
            ->getLocalParameters()
            ->clearTags()
            ->addTags($tags)
        ;

        return $this;
    }

    /**
     * Set the cursor mark to fetch.
     *
     * Cursor functionality requires a sort containing a uniqueKey field as tie breaker on top of your desired
     * sorts for the query.
     * Cursor functionality was introduced in Solr 4.7.
     *
     * @param string $cursormark
     *
     * @return self Provides fluent interface
     */
    public function setCursormark(string $cursormark): self
    {
        $this->setOption('cursormark', $cursormark);

        return $this;
    }

    /**
     * Get the cursor mark.
     *
     * @return string|null
     */
    public function getCursormark(): ?string
    {
        return $this->getOption('cursormark');
    }

    /**
     * Remove the cursor mark.
     *
     * @return self Provides fluent interface
     */
    public function clearCursormark(): self
    {
        $this->setOption('cursormark', null);

        return $this;
    }

    /**
     * Set SplitOnWhitespace option.
     *
     * Specifies whether the query parser splits the query text on whitespace before it's sent to be analyzed.
     *
     * The default is to split on whitespace, equivalent to &sow=true.
     * The sow parameter was introduced in Solr 6.5.
     *
     * @param bool $splitOnWhitespace
     *
     * @return self Provides fluent interface
     */
    public function setSplitOnWhitespace(bool $splitOnWhitespace): self
    {
        $this->setOption('splitonwhitespace', $splitOnWhitespace);

        return $this;
    }

    /**
     * Get SplitOnWhitespace option.
     *
     * @return bool|null
     */
    public function getSplitOnWhitespace(): ?bool
    {
        return $this->getOption('splitonwhitespace');
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'query':
                    $this->setQuery($value);
                    break;
                case 'filterquery':
                    $this->addFilterQueries($value);
                    break;
                case 'sort':
                    $this->addSorts($value);
                    break;
                case 'fields':
                    $this->addFields($value);
                    break;
                case 'rows':
                    $this->setRows((int) $value);
                    break;
                case 'start':
                    $this->setStart((int) $value);
                    break;
                case 'component':
                    $this->createComponents($value);
                    break;
                case 'cursormark':
                    $this->setCursormark($value);
                    break;
            }
        }
    }
}
