<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query;

use Solarium\Core\Query\Result\ResultInterface;

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
     * @param ResultInterface $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array;
}
