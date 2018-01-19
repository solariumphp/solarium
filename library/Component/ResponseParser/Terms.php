<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\Result\Terms\Field;
use Solarium\Component\Result\Terms\Result;
use Solarium\Component\Result\Terms\Terms as ResultTerms;
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
     * @param AbstractQuery $query
     * @param TermsComponent $terms
     * @param array $data
     *
     * @return Result|null
     */
    public function parse($query, $terms, $data)
    {
        $allTerms = [];

        if (isset($data['terms']) && is_array($data['terms'])) {
            $terms = [];
            foreach ($data['terms'] as $field => $termData) {
                $allTerms[] = $this->createTerms($termData);
                $terms[$field] = $this->createTerms($termData);
            }

            return new Result($this->createField($terms), $allTerms);
        }

        return null;
    }

    private function createField(array $terms)
    {
        return new Field(
            $terms
        );
    }

    private function createTerms(array $termData)
    {
        return new ResultTerms(
            $termData
        );
    }

}
