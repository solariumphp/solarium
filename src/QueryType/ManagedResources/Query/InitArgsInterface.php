<?php

namespace Solarium\QueryType\ManagedResources\Query;

/**
 * Init Args interface.
 */
interface InitArgsInterface
{
    /**
     * Sets the configuration parameters to be sent to Solr.
     *
     * @param array $initArgs
     *
     * @return self Provides fluent interface
     */
    public function setInitArgs(array $initArgs): self;

    /**
     * Returns the configuration parameters to be sent to Solr.
     *
     * @return array
     */
    public function getInitArgs(): array;
}
