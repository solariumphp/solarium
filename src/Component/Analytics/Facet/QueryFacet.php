<?php

declare(strict_types=1);

namespace Solarium\Component\Analytics\Facet;

/**
 * Query Facet.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class QueryFacet extends AbstractFacet
{
    /**
     * @var array
     */
    private $queries = [];

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return AbstractFacet::TYPE_QUERY;
    }

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->queries;
    }

    /**
     * @param array $queries
     *
     * @return QueryFacet
     */
    public function setQueries(array $queries): self
    {
        foreach ($queries as $key => $query) {
            $this->addQuery($key, $query);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $query
     *
     * @return $this
     */
    public function addQuery(string $key, string $query): self
    {
        $this->queries[$key] = $query;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return array_filter([
            'type' => $this->getType(),
            'queries' => $this->queries,
        ]);
    }
}
