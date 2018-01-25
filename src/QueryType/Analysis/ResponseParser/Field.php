<?php

namespace Solarium\QueryType\Analysis\ResponseParser;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;
use Solarium\Core\Query\Result\Result;
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
     * @param Result $result
     *
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();

        $items = [];

        if (isset($data['analysis'])) {
            $items = $this->parseAnalysis($result, $data['analysis']);
        }

        return $this->addHeaderInfo($data, ['items' => $items]);
    }

    /**
     * Parser.
     *
     * @param Result $result
     * @param array  $data
     *
     * @return Types[]
     */
    protected function parseAnalysis($result, $data)
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
     * @param Result $result
     * @param array  $typeData
     *
     * @return Types[]
     */
    protected function parseTypes($result, $typeData)
    {
        $query = $result->getQuery();

        $results = [];
        foreach ($typeData as $fieldKey => $fieldData) {
            $types = [];
            foreach ($fieldData as $typeKey => $typeData) {
                if ($query->getResponseWriter() == $query::WT_JSON) {
                    // fix for extra level for key fields
                    if (1 == count($typeData)) {
                        $typeData = current($typeData);
                    }
                    $typeData = $this->convertToKeyValueArray($typeData);
                }

                $classes = [];
                foreach ($typeData as $class => $analysis) {
                    if (is_string($analysis)) {
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
