<?php

namespace Solarium\Core\Query;

use Solarium\Client;
use Solarium\QueryType\Select\Query\Query;

class QueryBuilder
{
    /**
     * @var array
     */
    private $queryParts = array();

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $selectFields;

    /**
     * @var AbstractQuery
     */
    private $query;

    /**
     * @var bool
     */
    private $buildingWhere;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $field
     *
     * @return QueryBuilder
     */
    public function where($field)
    {
        $this->queryParts[] = $field . ':';

        $this->buildingWhere = true;

        return $this;
    }

    /**
     * @param array $select
     *
     * @return QueryBuilder
     */
    public function select($select)
    {
        $this->selectFields = is_array($select) ? $select : func_get_args();

        $this->query = $this->client->createSelect();

        return $this;
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     *
     * @return QueryBuilder
     */
    public function inDistance($lat, $long, $distance)
    {
        $this->ensureIsBuildingWhere();

        $parts = count($this->queryParts);
        $fieldName = $this->queryParts[$parts-1];

        $geoQuery = $this->query->getHelper()->geofilt($fieldName, $lat, $long, $distance);

        $this->queryParts[] = $geoQuery;

        $this->buildingWhere = false;

        return $this;
    }

    /**
     * @param array $collection
     *
     * @return QueryBuilder
     */
    public function inSet($collection)
    {
        $this->ensureIsBuildingWhere();

        $in = '(' . join(', ', $collection) . ')';

        $this->queryParts[] = $in;

        $this->buildingWhere = false;

        return $this;
    }

    /**
     * @param mixed $rangeStart
     * @param mixed $rangeEnd
     *
     * @return QueryBuilder
     */
    public function inRange($rangeStart, $rangeEnd)
    {
        $this->ensureIsBuildingWhere();

        $range = sprintf('[%s TO %s]', $rangeStart, $rangeEnd);

        $this->queryParts[] = $range;

        $this->buildingWhere = false;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return QueryBuilder
     */
    public function equals($value)
    {
        $this->ensureIsBuildingWhere();

        $this->queryParts[] = $value;

        $this->buildingWhere = false;

        return $this;
    }

    /**
     * @param string $field
     *
     * @return QueryBuilder
     */
    public function andWhere($field)
    {
        $this->queryParts[] = ' AND ' . $field . ':';

        $this->buildingWhere = true;

        return $this;
    }

    /**
     * @param string $field
     *
     * @return QueryBuilder
     */
    public function orWhere($field)
    {
        $this->queryParts[] = ' OR ' . $field . ':';

        $this->buildingWhere = true;

        return $this;
    }

    /**
     * @return AbstractQuery
     */
    public function getQuery()
    {
        $selectQuery = new Query();

        foreach ($this->selectFields as $field) {
            $selectQuery->addField($field);
        }

        $query = '';

        foreach ($this->queryParts as $queryPart) {
            $query .= $queryPart;
        }

        $selectQuery->setQuery($query);

        return $selectQuery;
    }

    /**
     * @throws \RuntimeException if no where is build
     */
    private function ensureIsBuildingWhere()
    {
        if ($this->buildingWhere === false) {
            throw new \RuntimeException('No where statement is currently build');
        }
    }
}