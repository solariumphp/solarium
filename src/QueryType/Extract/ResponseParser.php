<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Extract;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;

/**
 * Parse extract response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data.
     *
     * @param Result|ResultInterface $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        /** @var Query $query */
        $query = $result->getQuery();

        $parseResult = [];

        if (true === $query->getExtractOnly()) {
            $parseResult['file'] = $data['file'];
            $parseResult['fileMetadata'] = $data['file_metadata'];

            if ($query::WT_JSON === $query->getResponseWriter()) {
                $parseResult['fileMetadata'] = $this->convertToKeyValueArray($parseResult['fileMetadata']);
            }
        }

        return $parseResult;
    }
}
