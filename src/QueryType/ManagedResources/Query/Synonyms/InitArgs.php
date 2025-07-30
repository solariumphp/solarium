<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\ManagedResources\Query\Synonyms;

use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\ManagedResources\Query\InitArgsInterface;

/**
 * InitArgs.
 */
class InitArgs implements InitArgsInterface
{
    /**
     * Format 'solr'.
     */
    const FORMAT_SOLR = 'solr';

    /**
     * Whether or not to ignore the case.
     *
     * @var bool
     */
    protected $ignoreCase;

    /**
     * Format.
     *
     * @var string
     */
    protected $format;

    /**
     * Formats.
     *
     * @var array
     */
    protected $formats = [
        self::FORMAT_SOLR => 'solr',
    ];

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
     * Set format.
     *
     * Use one of the FORMAT_* constants as the value
     *
     * @param string $format
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return self Provides fluent interface
     */
    public function setFormat(string $format): self
    {
        if (!isset($this->formats[$format])) {
            throw new UnexpectedValueException(sprintf('Format unknown: %s', $format));
        }

        $this->format = $this->formats[$format];

        return $this;
    }

    /**
     * Get format.
     *
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
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
                case 'format':
                    $this->setFormat($value);
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

        if (isset($this->format)) {
            $initArgs['format'] = $this->format;
        }

        return $initArgs;
    }
}
