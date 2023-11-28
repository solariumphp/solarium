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
use Solarium\QueryType\Analysis\Result as AnalysisResult;
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
     * @return Types[]
     */
    protected function parseAnalysis(ResultInterface $result, array $data): array
    {
        $types = [];
        foreach ($data as $documentKey => $documentData) {
            $fields = $this->parseTypes($result, $documentData);
            $types[] = new AnalysisResult\ResultList($documentKey, $fields);
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
                if ($query::WT_JSON === $query->getResponseWriter()) {
                    // fix for extra level for key fields
                    if (1 === \count($typeData)) {
                        $typeData = current($typeData);
                    }
                    $typeData = $this->convertToKeyValueArray($typeData);
                }

                $classes = [];
                foreach ($typeData as $class => $analysis) {
                    if (\is_string($analysis)) {
                        $item = new Item(
                            [
                                'text' => $analysis,
                                'start' => null,
                                'end' => null,
                                'position' => null,
                                'positionHistory' => null,
                                'type' => null,
                            ]
                        );

                        $classes[] = new ResultList($class, [$item]);
                    } else {
                        $items = [];
                        foreach ($analysis as $itemData) {
                            $items[] = new Item($itemData);
                        }

                        $classes[] = new ResultList($class, $items);
                    }
                }

                $types[] = new ResultList($typeKey, $classes);
            }

            $results[] = new Types($fieldKey, $types);
        }

        return $results;
    }
}
