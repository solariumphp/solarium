<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query;

/**
 * Init Args interface.
 */
interface InitArgsInterface
{
    /**
     * Constructor.
     *
     * @param array $initArgs
     */
    public function __construct(array $initArgs = null);

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
