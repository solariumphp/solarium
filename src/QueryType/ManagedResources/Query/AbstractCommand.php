<?php

namespace Solarium\QueryType\ManagedResources\Query;

use Solarium\Core\Configurable;

/**
 * Command base class.
 */
abstract class AbstractCommand extends Configurable
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Returns request method.
     *
     * @return string
     */
    abstract public function getRequestMethod(): string;

    /**
     * Returns the data to be send to Solr.
     *
     * @return string
     */
    abstract public function getRawData(): string;

    /**
     * Returns the term to be send to Solr.
     *
     * @return string
     */
    abstract public function getTerm(): string;
}
