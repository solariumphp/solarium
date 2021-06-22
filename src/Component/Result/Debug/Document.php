<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Debug;

/**
 * Select component debug document result.
 */
class Document extends Detail implements \IteratorAggregate, \Countable
{
    /**
     * Key.
     *
     * @var string
     */
    protected $key;

    /**
     * Details.
     *
     * @var \Solarium\Component\Result\Debug\Detail[]
     */
    protected $details;

    /**
     * Constructor.
     *
     * @param string $key
     * @param bool   $match
     * @param float  $value
     * @param string $description
     * @param array  $details
     */
    public function __construct(string $key, bool $match, float $value, string $description, array $details)
    {
        parent::__construct($match, $value, $description);
        $this->key = $key;
        $this->details = $details;
    }

    /**
     * Get key value for this document.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get details.
     *
     * @return \Solarium\Component\Result\Debug\Detail[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->details);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->details);
    }

    public function __toString()
    {
        $string = '';
        foreach ($this->getDetails() as $detail) {
            $string .= '  '.$detail.PHP_EOL;
        }

        return $string;
    }
}
