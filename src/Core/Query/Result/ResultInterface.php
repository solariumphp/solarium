<?php

namespace Solarium\Core\Query\Result;

use Solarium\Core\Client\Response;
use Solarium\Core\Query\AbstractQuery;

/**
 * Query result interface.
 */
interface ResultInterface
{
    /**
     * Get response object.
     *
     * This is the raw HTTP response object, not the parsed data!
     *
     * @return Response
     */
    public function getResponse(): Response;

    /**
     * Get query instance.
     *
     * @return AbstractQuery
     */
    public function getQuery(): AbstractQuery;

    /**
     * Get Solr response data.
     *
     * Includes a lazy loading mechanism: JSON body data is decoded on first use and then saved for reuse.
     *
     * @return array
     */
    public function getData(): array;
}
