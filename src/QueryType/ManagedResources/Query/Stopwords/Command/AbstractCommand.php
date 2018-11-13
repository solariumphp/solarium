<?php

namespace Solarium\QueryType\ManagedResources\Query\Stopwords\Command;

use Solarium\Core\Configurable;

/**
 * Stopwords query command base class.
 */
abstract class AbstractCommand extends Configurable
{
    /**
     * Returns command type, for use in adapters.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Returns request method.
     *
     * @return string
     */
    abstract public function getRequestMethod();

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
