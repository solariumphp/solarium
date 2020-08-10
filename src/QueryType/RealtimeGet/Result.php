<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\RealtimeGet;

use Solarium\Core\Query\DocumentInterface;
use Solarium\QueryType\Select\Result\Result as BaseResult;

/**
 * RealtimeGet query results.
 *
 * Extends the standard select result with a accessor method for the first document
 */
class Result extends BaseResult
{
    /**
     * Get first document in set.
     *
     * @return DocumentInterface
     */
    public function getDocument(): DocumentInterface
    {
        $docs = $this->getDocuments();

        return reset($docs);
    }
}
