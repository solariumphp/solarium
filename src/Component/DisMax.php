<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\DisMax\BoostQuery;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\DisMax as RequestBuilder;
use Solarium\Exception\InvalidArgumentException;

/**
 * DisMax component.
 *
 * @see https://solr.apache.org/guide/the-dismax-query-parser.html
 */
class DisMax extends AbstractComponent
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'queryparser' => 'dismax',
    ];

    /**
     * Boostqueries.
     *
     * @var BoostQuery[]
     */
    protected $boostQueries = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_DISMAX;
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
     * Set QueryAlternative option.
     *
     * If specified, this query will be used (and parsed by default using
     * standard query parsing syntax) when the main query string is not
     * specified or blank.
     *
     * @param string $queryAlternative
     *
     * @return self Provides fluent interface
     */
    public function setQueryAlternative($queryAlternative): self
    {
        $this->setOption('queryalternative', $queryAlternative);

        return $this;
    }

    /**
     * Get QueryAlternative option.
     *
     * @return string|null
     */
    public function getQueryAlternative(): ?string
    {
        return $this->getOption('queryalternative');
    }

    /**
     * Set QueryFields option.
     *
     * List of fields and the "boosts" to associate with each of them when
     * building DisjunctionMaxQueries from the user's query.
     *
     * The format supported is "fieldOne^2.3 fieldTwo fieldThree^0.4"
     *
     * @param string $queryFields
     *
     * @return self Provides fluent interface
     */
    public function setQueryFields(string $queryFields): self
    {
        $this->setOption('queryfields', $queryFields);

        return $this;
    }

    /**
     * Get QueryFields option.
     *
     * @return string|null
     */
    public function getQueryFields(): ?string
    {
        return $this->getOption('queryfields');
    }

    /**
     * Set MinimumMatch option.
     *
     * This option makes it possible to say that a certain minimum number of
     * clauses must match. See Solr manual for details.
     *
     * @param string $minimumMatch
     *
     * @return self Provides fluent interface
     */
    public function setMinimumMatch(string $minimumMatch): self
    {
        $this->setOption('minimummatch', $minimumMatch);

        return $this;
    }

    /**
     * Get MinimumMatch option.
     *
     * @return string|null
     */
    public function getMinimumMatch(): ?string
    {
        return $this->getOption('minimummatch');
    }

    /**
     * Set PhraseFields option.
     *
     * This param can be used to "boost" the score of documents in cases
     * where all of the terms in the "q" param appear in close proximity.
     *
     * Format is: "fieldA^1.0 fieldB^2.2"
     *
     * @param string $phraseFields
     *
     * @return self Provides fluent interface
     */
    public function setPhraseFields(string $phraseFields): self
    {
        $this->setOption('phrasefields', $phraseFields);

        return $this;
    }

    /**
     * Get PhraseFields option.
     *
     * @return string|null
     */
    public function getPhraseFields(): ?string
    {
        return $this->getOption('phrasefields');
    }

    /**
     * Set PhraseSlop option.
     *
     * Amount of slop on phrase queries built for "pf" fields
     * (affects boosting)
     *
     * @param int $phraseSlop
     *
     * @return self Provides fluent interface
     */
    public function setPhraseSlop(int $phraseSlop): self
    {
        $this->setOption('phraseslop', $phraseSlop);

        return $this;
    }

    /**
     * Get PhraseSlop option.
     *
     * @return int|null
     */
    public function getPhraseSlop(): ?int
    {
        return $this->getOption('phraseslop');
    }

    /**
     * Set QueryPhraseSlop option.
     *
     * Amount of slop on phrase queries explicitly included in the user's
     * query string (in qf fields; affects matching)
     *
     * @param int $queryPhraseSlop
     *
     * @return self Provides fluent interface
     */
    public function setQueryPhraseSlop(int $queryPhraseSlop): self
    {
        $this->setOption('queryphraseslop', $queryPhraseSlop);

        return $this;
    }

    /**
     * Get QueryPhraseSlop option.
     *
     * @return int|null
     */
    public function getQueryPhraseSlop(): ?int
    {
        return $this->getOption('queryphraseslop');
    }

    /**
     * Set Tie option.
     *
     * Float value to use as tiebreaker in DisjunctionMaxQueries
     *
     * @param float $tie
     *
     * @return self Provides fluent interface
     */
    public function setTie(float $tie): self
    {
        $this->setOption('tie', $tie);

        return $this;
    }

    /**
     * Get Tie option.
     *
     * @return float|null
     */
    public function getTie(): ?float
    {
        return $this->getOption('tie');
    }

    /**
     * Set BoostQuery option.
     *
     * A raw query string (in the SolrQuerySyntax) that will be included
     * with the user's query to influence the score.
     *
     * @param string $boostQuery
     *
     * @return self Provides fluent interface
     */
    public function setBoostQuery(string $boostQuery): self
    {
        $this->clearBoostQueries();
        $this->addBoostQuery(['key' => 0, 'query' => $boostQuery]);

        return $this;
    }

    /**
     * Get BoostQuery option.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getBoostQuery(string $key = null): ?string
    {
        if (null !== $key) {
            if (\array_key_exists($key, $this->boostQueries)) {
                return $this->boostQueries[$key]->getQuery();
            }
        } elseif (!empty($this->boostQueries)) {
            /** @var BoostQuery[] $boostQueries */
            $boostQueries = array_values($this->boostQueries);

            return $boostQueries[0]->getQuery();
        } elseif (\array_key_exists('boostquery', $this->options)) {
            return $this->options['boostquery'];
        }

        return null;
    }

    /**
     * Add a boost query.
     *
     * Supports a boostquery instance or a config array, in that case a new
     * boostquery instance wil be created based on the options.
     *
     * @param BoostQuery|array $boostQuery
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addBoostQuery($boostQuery): self
    {
        if (\is_array($boostQuery)) {
            $boostQuery = new BoostQuery($boostQuery);
        }

        $key = $boostQuery->getKey();

        if (0 === \strlen($key)) {
            throw new InvalidArgumentException('A boostquery must have a key value');
        }

        //double add calls for the same BQ are ignored, but non-unique keys cause an exception
        if (\array_key_exists($key, $this->boostQueries) && $this->boostQueries[$key] !== $boostQuery) {
            throw new InvalidArgumentException('A boostquery must have a unique key value within a query');
        }

        $this->boostQueries[$key] = $boostQuery;

        return $this;
    }

    /**
     * Add multiple boostqueries.
     *
     * @param array $boostQueries
     *
     * @return self Provides fluent interface
     */
    public function addBoostQueries(array $boostQueries): self
    {
        foreach ($boostQueries as $key => $boostQuery) {
            // in case of a config array: add key to config
            if (\is_array($boostQuery) && !isset($boostQuery['key'])) {
                $boostQuery['key'] = $key;
            }

            $this->addBoostQuery($boostQuery);
        }

        return $this;
    }

    /**
     * Get all boostqueries.
     *
     * @return BoostQuery[]
     */
    public function getBoostQueries(): array
    {
        return $this->boostQueries;
    }

    /**
     * Remove a single boostquery.
     *
     * You can remove a boostquery by passing its key, or by passing the boostquery instance
     *
     * @param string|BoostQuery $boostQuery
     *
     * @return self Provides fluent interface
     */
    public function removeBoostQuery($boostQuery): self
    {
        if (\is_object($boostQuery)) {
            $boostQuery = $boostQuery->getKey();
        }

        if ($boostQuery && isset($this->boostQueries[$boostQuery])) {
            unset($this->boostQueries[$boostQuery]);
        }

        return $this;
    }

    /**
     * Remove all boostqueries.
     *
     * @return self Provides fluent interface
     */
    public function clearBoostQueries(): self
    {
        $this->boostQueries = [];

        return $this;
    }

    /**
     * Set multiple boostqueries.
     *
     * This overwrites any existing boostqueries
     *
     * @param array $boostQueries
     *
     * @return self Provides fluent interface
     */
    public function setBoostQueries(array $boostQueries): self
    {
        $this->clearBoostQueries();
        $this->addBoostQueries($boostQueries);

        return $this;
    }

    /**
     * Set BoostFunctions option.
     *
     * Functions (with optional boosts) that will be included in the
     * user's query to influence the score.
     *
     * Format is: "funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2"
     *
     * @param string $boostFunctions
     *
     * @return self Provides fluent interface
     */
    public function setBoostFunctions(string $boostFunctions): self
    {
        $this->setOption('boostfunctions', $boostFunctions);

        return $this;
    }

    /**
     * Get BoostFunctions option.
     *
     * @return string|null
     */
    public function getBoostFunctions(): ?string
    {
        return $this->getOption('boostfunctions');
    }

    /**
     * Set QueryParser option.
     *
     * Can be used to enable edismax
     *
     * @since 2.1.0
     *
     * @param string $parser
     *
     * @return self Provides fluent interface
     */
    public function setQueryParser(string $parser): self
    {
        $this->setOption('queryparser', $parser);

        return $this;
    }

    /**
     * Get QueryParser option.
     *
     * @since 2.1.0
     *
     * @return string
     */
    public function getQueryParser(): string
    {
        return $this->getOption('queryparser');
    }
}
