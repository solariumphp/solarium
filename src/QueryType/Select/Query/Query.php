<?php

namespace Solarium\QueryType\Select\Query;

use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\ComponentAwareQueryTrait;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
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
use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\RequestBuilder;
use Solarium\QueryType\Select\ResponseParser;

/**
 * Select Query.
 *
 * Can be used to select documents and/or facets from Solr. This querytype has
 * lots of options and there are many Solarium subclasses for it.
 * See the Solr documentation and the relevant Solarium classes for more info.
 */
class Query extends AbstractQuery implements ComponentAwareQueryInterface, QueryInterface
{
    use ComponentAwareQueryTrait;
    use MoreLikeThisTrait;
    use SpellcheckTrait;
    use SuggesterTrait;
    use DebugTrait;
    use SpatialTrait;
    use FacetSetTrait;
    use DisMaxTrait;
    use EDisMaxTrait;
    use HighlightingTrait;
    use GroupingTrait;
    use DistributedSearchTrait;
    use StatsTrait;
    use QueryElevationTrait;
    use ReRankQueryTrait;
    use QueryTrait;

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
        'resultclass' => 'Solarium\QueryType\Select\Result\Result',
        'documentclass' => 'Solarium\QueryType\Select\Result\Document',
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

    public function __construct($options = null)
    {
        $this->componentTypes = [
            ComponentAwareQueryInterface::COMPONENT_MORELIKETHIS => 'Solarium\Component\MoreLikeThis',
            ComponentAwareQueryInterface::COMPONENT_SPELLCHECK => 'Solarium\Component\Spellcheck',
            ComponentAwareQueryInterface::COMPONENT_SUGGESTER => 'Solarium\Component\Suggester',
            ComponentAwareQueryInterface::COMPONENT_DEBUG => 'Solarium\Component\Debug',
            ComponentAwareQueryInterface::COMPONENT_SPATIAL => 'Solarium\Component\Spatial',
            ComponentAwareQueryInterface::COMPONENT_FACETSET => 'Solarium\Component\FacetSet',
            ComponentAwareQueryInterface::COMPONENT_DISMAX => 'Solarium\Component\DisMax',
            ComponentAwareQueryInterface::COMPONENT_EDISMAX => 'Solarium\Component\EdisMax',
            ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING => 'Solarium\Component\Highlighting\Highlighting',
            ComponentAwareQueryInterface::COMPONENT_GROUPING => 'Solarium\Component\Grouping',
            ComponentAwareQueryInterface::COMPONENT_DISTRIBUTEDSEARCH => 'Solarium\Component\DistributedSearch',
            ComponentAwareQueryInterface::COMPONENT_STATS => 'Solarium\Component\Stats\Stats',
            ComponentAwareQueryInterface::COMPONENT_QUERYELEVATION => 'Solarium\Component\QueryElevation',
            ComponentAwareQueryInterface::COMPONENT_RERANKQUERY => 'Solarium\Component\ReRankQuery',
        ];

        parent::__construct($options);
    }

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType()
    {
        return Client::QUERY_SELECT;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
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
    public function setQueryDefaultOperator($operator)
    {
        return $this->setOption('querydefaultoperator', $operator);
    }

    /**
     * Get the default query operator.
     *
     * @return null|string
     */
    public function getQueryDefaultOperator()
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
    public function setQueryDefaultField($field)
    {
        return $this->setOption('querydefaultfield', $field);
    }

    /**
     * Get the default query field.
     *
     * @return null|string
     */
    public function getQueryDefaultField()
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
    public function setStart($start)
    {
        return $this->setOption('start', $start);
    }

    /**
     * Get the start offset.
     *
     * @return int
     */
    public function getStart()
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
    public function setDocumentClass($value)
    {
        return $this->setOption('documentclass', $value);
    }

    /**
     * Get the current documentclass option.
     *
     * The value is a classname, not an instance
     *
     * @return string
     */
    public function getDocumentClass()
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
    public function setRows($rows)
    {
        return $this->setOption('rows', $rows);
    }

    /**
     * Get the number of rows.
     *
     * @return int
     */
    public function getRows()
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
    public function addField($field)
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
    public function addFields($fields)
    {
        if (is_string($fields)) {
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
    public function removeField($field)
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
    public function clearFields()
    {
        $this->fields = [];

        return $this;
    }

    /**
     * Get the list of fields.
     *
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->fields);
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields)
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
    public function addSort($sort, $order)
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
    public function addSorts(array $sorts)
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
    public function removeSort($sort)
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
    public function clearSorts()
    {
        $this->sorts = [];

        return $this;
    }

    /**
     * Get a list of the sorts.
     *
     * @return array
     */
    public function getSorts()
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
    public function setSorts($sorts)
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
    public function createFilterQuery($options = null)
    {
        if (is_string($options)) {
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
     *
     * @param FilterQuery|array $filterQuery
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addFilterQuery($filterQuery)
    {
        if (is_array($filterQuery)) {
            $filterQuery = new FilterQuery($filterQuery);
        }

        $key = $filterQuery->getKey();

        if (0 === strlen($key)) {
            throw new InvalidArgumentException('A filterquery must have a key value');
        }

        //double add calls for the same FQ are ignored, but non-unique keys cause an exception
        if (array_key_exists($key, $this->filterQueries) && $this->filterQueries[$key] !== $filterQuery) {
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
    public function addFilterQueries(array $filterQueries)
    {
        foreach ($filterQueries as $key => $filterQuery) {
            // in case of a config array: add key to config
            if (is_array($filterQuery) && !isset($filterQuery['key'])) {
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
     * @return string
     */
    public function getFilterQuery($key)
    {
        if (isset($this->filterQueries[$key])) {
            return $this->filterQueries[$key];
        }
    }

    /**
     * Get all filterqueries.
     *
     * @return FilterQuery[]
     */
    public function getFilterQueries()
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
    public function removeFilterQuery($filterQuery)
    {
        if (is_object($filterQuery)) {
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
    public function clearFilterQueries()
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
     */
    public function setFilterQueries($filterQueries)
    {
        $this->clearFilterQueries();
        $this->addFilterQueries($filterQueries);
    }

    /**
     * Add a tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function addTag($tag)
    {
        $this->tags[$tag] = true;

        return $this;
    }

    /**
     * Add tags.
     *
     * @param array $tags
     *
     * @return self Provides fluent interface
     */
    public function addTags($tags)
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        return $this;
    }

    /**
     * Get all tagss.
     *
     * @return array
     */
    public function getTags()
    {
        return array_keys($this->tags);
    }

    /**
     * Remove a tag.
     *
     * @param string $tag
     *
     * @return self Provides fluent interface
     */
    public function removeTag($tag)
    {
        if (isset($this->tags[$tag])) {
            unset($this->tags[$tag]);
        }

        return $this;
    }

    /**
     * Remove all tags.
     *
     * @return self Provides fluent interface
     */
    public function clearTags()
    {
        $this->tags = [];

        return $this;
    }

    /**
     * Set multiple tags.
     *
     * This overwrites any existing tags
     *
     * @param array $tags
     *
     * @return self Provides fluent interface
     */
    public function setTags($tags)
    {
        $this->clearTags();

        return $this->addTags($tags);
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
    public function setCursormark($cursormark)
    {
        return $this->setOption('cursormark', $cursormark);
    }

    /**
     * Get the cursor mark.
     *
     * @return string|null
     */
    public function getCursormark()
    {
        return $this->getOption('cursormark');
    }

    /**
     * Remove the cursor mark.
     *
     * @return self Provides fluent interface
     */
    public function clearCursormark()
    {
        return $this->setOption('cursormark', null);
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
    public function setSplitOnWhitespace($splitOnWhitespace)
    {
        return $this->setOption('splitonwhitespace', $splitOnWhitespace);
    }

    /**
     * Get SplitOnWhitespace option.
     *
     * @return bool|null
     */
    public function getSplitOnWhitespace()
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
                case 'tag':
                    if (!is_array($value)) {
                        $value = explode(',', $value);
                    }
                    $this->addTags($value);
                    break;
                case 'cursormark':
                    $this->setCursormark($value);
                    break;
            }
        }
    }
}
