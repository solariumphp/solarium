<?php

namespace Solarium\QueryType\Terms;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;

/**
 * Parse Terms response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse($result)
    {
        $termResults = [];

        $data = $result->getData();

        /*
         * @var Query
         */
        $query = $result->getQuery();

        // Special case to handle Solr 1.4 data
        if (isset($data['terms']) && count($data['terms']) == count($query->getFields()) * 2) {
            $data['terms'] = $this->convertToKeyValueArray($data['terms']);
        }

        foreach ($query->getFields() as $field) {
            $field = trim($field);

            if (isset($data['terms'][$field])) {
                $terms = $data['terms'][$field];
                if ($query->getResponseWriter() == $query::WT_JSON) {
                    $terms = $this->convertToKeyValueArray($terms);
                }
                $termResults[$field] = $terms;
            }
        }

        return $this->addHeaderInfo($data, ['results' => $termResults]);
    }
}
