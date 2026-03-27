<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Analysis\Result\Item;
use Solarium\QueryType\Analysis\Result\ResultList;
use Solarium\QueryType\Analysis\Result\Types;

/**
 * Parse document analysis response data.
 */
class Field extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param ResultInterface $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();

        $items = [];

        if (isset($data['analysis'])) {
            $items = $this->parseAnalysis($result, $data['analysis']);
        }

        return ['items' => $items];
    }

    /**
     * Parser.
     *
     * @param ResultInterface $result
     * @param array           $data
     *
     * @return ResultList[]
     */
    protected function parseAnalysis(ResultInterface $result, array $data): array
    {
        $types = [];
        foreach ($data as $documentKey => $documentData) {
            $fields = $this->parseTypes($result, $documentData);
            $types[] = new ResultList($documentKey, $fields);
        }

        return $types;
    }

    /**
     * Parse analysis types and items.
     *
     * @param ResultInterface $result
     * @param array           $data
     *
     * @return Types[]
     */
    protected function parseTypes(ResultInterface $result, array $data): array
    {
        $query = $result->getQuery();

        $results = [];
        foreach ($data as $fieldKey => $fieldData) {
            $types = [];
            foreach ($fieldData as $typeKey => $typeData) {
                $classes = [];
                if ('query' === $typeKey || array_is_list($typeData)) {
                    // document 'query' data or field 'index' data
                    if ($query::WT_JSON === $query->getResponseWriter()) {
                        $typeData = $this->convertToKeyValueArray($typeData);
                    }

                    foreach ($typeData as $class => $analysis) {
                        $classes[] = $this->createResultList($class, $analysis);
                    }
                } else {
                    // document 'index' data
                    if ($query::WT_JSON === $query->getResponseWriter()) {
                        $typeData = array_map([$this, 'convertToKeyValueArray'], $typeData);
                    }

                    foreach ($typeData as $valueData) {
                        foreach ($valueData as $class => $analysis) {
                            $classes[] = $this->createResultList($class, $analysis);
                        }
                    }
                }

                $types[] = new ResultList($typeKey, $classes);
            }

            $results[] = new Types($fieldKey, $types);
        }

        return $results;
    }

    /**
     * Create the result list for a single query or field value.
     *
     * @param string       $class
     * @param string|array $analysis
     *
     * @return ResultList
     */
    protected function createResultList(string $class, string|array $analysis): ResultList
    {
        $items = [];

        if (\is_string($analysis)) {
            $items[] = new Item(
                [
                    'text' => $analysis,
                    'start' => null,
                    'end' => null,
                    'position' => null,
                    'positionHistory' => null,
                    'type' => null,
                ]
            );
        } else {
            foreach ($analysis as $itemData) {
                $items[] = new Item($itemData);
            }
        }

        return new ResultList($class, $items);
    }
}
