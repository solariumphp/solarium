<?php

namespace Solarium\QueryType\Terms;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;

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
    public function parse(ResultInterface $result): array
    {
        $termResults = [];

        $data = $result->getData();

        /** @var Query $query */
        $query = $result->getQuery();

        foreach ($query->getFields() as $field) {
            $field = trim($field);

            if (isset($data['terms'][$field])) {
                $terms = $data['terms'][$field];
                // There seems to be a bug in Solr that json.nl=flat is ignored in a distributed search on Solr
                // Cloud. In that case the "map" format is returned which doesn't need to be converted. But we don't
                // use it in general because it has limitations for some components.
                if (isset($terms[0]) && $query->getResponseWriter() == $query::WT_JSON) {
                    // We have a "flat" json result.
                    $terms = $this->convertToKeyValueArray($terms);
                }
                $termResults[$field] = $terms;
            }
        }

        return $this->addHeaderInfo($data, ['results' => $termResults]);
    }
}
