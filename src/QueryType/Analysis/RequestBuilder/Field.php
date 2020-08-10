<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\RequestBuilder;

use Solarium\Core\Client\Request;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\QueryInterface;
use Solarium\QueryType\Analysis\Query\Field as QueryField;

/**
 * Build a field analysis request.
 */
class Field extends RequestBuilder
{
    /**
     * Build request for an analysis field query.
     *
     * @param QueryInterface|QueryField $query
     *
     * @return Request
     */
    public function build(AbstractQuery $query): Request
    {
        $request = parent::build($query);

        $request->addParam('analysis.fieldvalue', $query->getFieldValue());
        $request->addParam('analysis.fieldname', $query->getFieldName());
        $request->addParam('analysis.fieldtype', $query->getFieldType());

        return $request;
    }
}
