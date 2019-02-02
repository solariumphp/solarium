<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\Result\Terms\Field;
use Solarium\Component\Result\Terms\Result;
use Solarium\Component\Terms as TermsComponent;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractResponseParser;

/**
 * Parse Terms response data.
 */
class Terms extends AbstractResponseParser implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery  $query
     * @param TermsComponent $terms
     * @param array          $data
     *
     * @return Result|null
     */
    public function parse($query, $terms, $data)
    {
        $allTerms = [];

        if (isset($data['terms']) && is_array($data['terms'])) {
            $terms = [];
            foreach ($data['terms'] as $field => $termData) {
                // There seems to be a bug in Solr that json.nl=flat is ignored in a distributed search on Solr
                // Cloud. In that case the "map" format is returned which doesn't need to be converted. But we don't
                // use it in general because it has limitations for some components.
                if ($query->getResponseWriter() == $query::WT_JSON && isset($termData[0])) {
                    // We have a "flat" json result.
                    $termData = $this->convertToKeyValueArray($termData);
                }
                $allTerms[$field] = $termData;
                $terms[$field] = new Field($termData);
            }

            return new Result($terms, $allTerms);
        }

        return null;
    }
}
