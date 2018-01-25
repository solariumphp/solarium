<?php

namespace Solarium\QueryType\RealtimeGet;

use Solarium\QueryType\Select\Result\DocumentInterface;
use Solarium\QueryType\Select\Result\Result as BaseResult;

/**
 * RealtimeGet query results.
 *
 * Extends the standard select result with a accessor method for the first document
 */
class Result extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Get first document in set.
     *
     * @return DocumentInterface
     */
    public function getDocument()
    {
        $docs = $this->getDocuments();

        return reset($docs);
    }
}
