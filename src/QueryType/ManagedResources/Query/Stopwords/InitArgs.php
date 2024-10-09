<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Stopwords;

use Solarium\QueryType\ManagedResources\Query\InitArgsInterface;

/**
 * InitArgs.
 */
class InitArgs implements InitArgsInterface
{
    /**
     * Whether or not to ignore the case.
     *
     * @var bool
     */
    protected $ignoreCase;

    /**
     * Constructor.
     *
     * @param array|null $initArgs
     */
    public function __construct(?array $initArgs = null)
    {
        if (null !== $initArgs) {
            $this->setInitArgs($initArgs);
        }
    }

    /**
     * Set ignore case.
     *
     * @param bool $ignoreCase
     *
     * @return self Provides fluent interface
     */
    public function setIgnoreCase(bool $ignoreCase): self
    {
        $this->ignoreCase = $ignoreCase;

        return $this;
    }

    /**
     * Get ignore case.
     *
     * @return bool|null
     */
    public function getIgnoreCase(): ?bool
    {
        return $this->ignoreCase;
    }

    /**
     * Sets the configuration parameters to be sent to Solr.
     *
     * @param array $initArgs
     *
     * @return self Provides fluent interface
     */
    public function setInitArgs(array $initArgs): self
    {
        foreach ($initArgs as $arg => $value) {
            switch ($arg) {
                case 'ignoreCase':
                    $this->setIgnoreCase($value);
                    break;
            }
        }

        return $this;
    }

    /**
     * Returns the configuration parameters to be sent to Solr.
     *
     * @return array
     */
    public function getInitArgs(): array
    {
        $initArgs = [];

        if (isset($this->ignoreCase)) {
            $initArgs['ignoreCase'] = $this->ignoreCase;
        }

        return $initArgs;
    }
}
