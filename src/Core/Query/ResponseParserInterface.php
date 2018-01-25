<?php

namespace Solarium\Core\Query;

/**
 * Interface for response parsers.
 *
 * Most {@link Solarium\Client\Adapter} implementations will use HTTP for
 * communicating with Solr. While the HTTP part is adapter-specific, the parsing
 * of the response into Solarium\Result classes is not. This abstract class is
 * the base for several response handlers that do just that for the various
 * querytypes.
 */
interface ResponseParserInterface
{
    /**
     * Get a Result object for the given data.
     *
     * When this method is called the actual response parsing is started.
     *
     * @param \Solarium\Core\Query\Result\Result $result
     *
     * @return mixed
     */
    public function parse($result);
}
