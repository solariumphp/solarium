<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
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
     * @param TermsComponent $component
     * @param array          $data
     *
     * @return Result|null
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $component, array $data): ?Result
    {
        $allTerms = [];

        if (isset($data['terms']) && \is_array($data['terms'])) {
            $terms = [];
            foreach ($data['terms'] as $field => $termData) {
                // There seems to be a bug in Solr that json.nl=flat is ignored in a distributed search on Solr
                // Cloud. In that case the "map" format is returned which doesn't need to be converted. But we don't
                // use it in general because it has limitations for some components.
                if (isset($termData[0]) && $query && $query::WT_JSON === $query->getResponseWriter()) {
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
